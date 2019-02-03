<?php
// scrypt for backup and restore postgres database

function dl_file($file){
   if (!is_file($file)) { die("<b>404 File not found!</b>"); }
   $len = filesize($file);
   $filename = basename($file);
   $file_extension = strtolower(substr(strrchr($filename,"."),1));
   $ctype="application/force-download";
   header("Pragma: public");
   header("Expires: 0");
   header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
   header("Cache-Control: public");
   header("Content-Description: File Transfer");
   header("Content-Type: $ctype");
   $header="Content-Disposition: attachment; filename=".$filename.";";
   header($header );
   header("Content-Transfer-Encoding: binary");
   header("Content-Length: ".$len);
   @readfile($file);
   exit;
}

$action  = $_POST["actionButton"];
$ficheiro=$_FILES["path"]["name"];
switch ($action) {
    case "Import":
      $dbname = "minukube-php"; //database name
      $dbconn = pg_pconnect("host=postgres-service port=5432 dbname=$dbname 
user=test password=test"); //connectionstring
      if (!$dbconn) {
        echo "Can't connect.\n";
        exit;
      }
      $back = fopen($ficheiro,"r");
      $contents = fread($back, filesize($ficheiro));
      $res = pg_query(utf8_encode($contents));
      echo "Upload Ok";
      fclose($back);
  break;
  case "Export":
  $dbname = "minukube-php"; //database name
  $dbconn = pg_pconnect("host=postgres-service port=5432 dbname=$dbname 
user=test password=test"); //connectionstring
  if (!$dbconn) {
    echo "Can't connect.\n";
  exit;
  }
  $back = fopen("$dbname.sql","w");
  $res = pg_query(" select relname as tablename
                    from pg_class where relkind in ('r')
                    and relname not like 'pg_%' and relname not like 
'sql_%' order by tablename");
  $str="";
  while($row = pg_fetch_row($res))
  {
    $table = $row[0];
    $str .= "\n--\n";
    $str .= "-- Estrutura da tabela '$table'";
    $str .= "\n--\n";
    $str .= "\nDROP TABLE $table CASCADE;";
    $str .= "\nCREATE TABLE $table (";
    $res2 = pg_query("
    SELECT  attnum,attname , typname , atttypmod-4 , attnotnull 
,atthasdef ,adsrc AS def
    FROM pg_attribute, pg_class, pg_type, pg_attrdef WHERE 
pg_class.oid=attrelid
    AND pg_type.oid=atttypid AND attnum>0 AND pg_class.oid=adrelid AND 
adnum=attnum
    AND atthasdef='t' AND lower(relname)='$table' UNION
    SELECT attnum,attname , typname , atttypmod-4 , attnotnull , 
atthasdef ,'' AS def
    FROM pg_attribute, pg_class, pg_type WHERE pg_class.oid=attrelid
    AND pg_type.oid=atttypid AND attnum>0 AND atthasdef='f' AND 
lower(relname)='$table' ");                                             
    while($r = pg_fetch_row($res2))
    {
    $str .= "\n" . $r[1]. " " . $r[2];
     if ($r[2]=="varchar")
    {
    $str .= "(".$r[3] .")";
    }
    if ($r[4]=="t")
    {
    $str .= " NOT NULL";
    }
    if ($r[5]=="t")
    {
    $str .= " DEFAULT ".$r[6];
    }
    $str .= ",";
    }
    $str=rtrim($str, ",");  
    $str .= "\n);\n";
    $str .= "\n--\n";
    $str .= "-- Creating data for '$table'";
    $str .= "\n--\n\n";

    
    $res3 = pg_query("SELECT * FROM $table");
    while($r = pg_fetch_row($res3))
    {
      $sql = "INSERT INTO $table VALUES ('";
      $sql .= utf8_decode(implode("','",$r));
      $sql .= "');";
      $str = str_replace("''","NULL",$str);
      $str .= $sql;  
      $str .= "\n";
    }
    
     $res1 = pg_query("SELECT pg_index.indisprimary,
            pg_catalog.pg_get_indexdef(pg_index.indexrelid)
        FROM pg_catalog.pg_class c, pg_catalog.pg_class c2,
            pg_catalog.pg_index AS pg_index
        WHERE c.relname = '$table'
            AND c.oid = pg_index.indrelid
            AND pg_index.indexrelid = c2.oid
            AND pg_index.indisprimary");
    while($r = pg_fetch_row($res1))
    {
    $str .= "\n\n--\n";
    $str .= "-- Creating index for '$table'";
    $str .= "\n--\n\n";
    $t = str_replace("CREATE UNIQUE INDEX", "", $r[1]);
    $t = str_replace("USING btree", "|", $t);
    // Next Line Can be improved!!!
    $t = str_replace("ON", "|", $t);
    $Temparray = explode("|", $t);
    $str .= "ALTER TABLE ONLY ". $Temparray[1] . " ADD CONSTRAINT " . 
$Temparray[0] . " PRIMARY KEY " . $Temparray[2] .";\n";
    }   
  }
  $res = pg_query(" SELECT
  cl.relname AS tabela,ct.conname,
   pg_get_constraintdef(ct.oid)
   FROM pg_catalog.pg_attribute a
   JOIN pg_catalog.pg_class cl ON (a.attrelid = cl.oid AND cl.relkind = 'r')
   JOIN pg_catalog.pg_namespace n ON (n.oid = cl.relnamespace)
   JOIN pg_catalog.pg_constraint ct ON (a.attrelid = ct.conrelid AND
   ct.confrelid != 0 AND ct.conkey[1] = a.attnum)
   JOIN pg_catalog.pg_class clf ON (ct.confrelid = clf.oid AND 
clf.relkind = 'r')
   JOIN pg_catalog.pg_namespace nf ON (nf.oid = clf.relnamespace)
   JOIN pg_catalog.pg_attribute af ON (af.attrelid = ct.confrelid AND
   af.attnum = ct.confkey[1]) order by cl.relname ");
  while($row = pg_fetch_row($res))
  {
    $str .= "\n\n--\n";
    $str .= "-- Creating relacionships for '".$row[0]."'";
    $str .= "\n--\n\n";
    $str .= "ALTER TABLE ONLY ".$row[0] . " ADD CONSTRAINT " . $row[1] . 
" " . $row[2] . ";";
  }       
  fwrite($back,$str);
  fclose($back);
  dl_file("$dbname.sql");
  break;
}
?>
<html>
<head>
</head>
<body>
  <p> Please import the file employee.sql </p>
<form id="dataForm" name="dataForm" method="post" 
enctype="multipart/form-data" action="">
    <input type="file" name="path" id="path" style="width:300px"/>
    <input type="submit" value="Import" name="actionButton" 
id="actionButton" >
    <input type="submit" value="Export" name="actionButton" 
id="actionButton" >
</form>
</body>
</html>