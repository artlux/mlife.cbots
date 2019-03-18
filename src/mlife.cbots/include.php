<?
spl_autoload_register(function ($class) {
    $fileName = str_replace("//",'/',__DIR__. '/lib/' . strtolower(str_replace('Table','',$class))) . '.php';
	$fileName = str_replace('mlife\\cbots\\','',$fileName);
	$fileName = str_replace('\\','/',$fileName);
	require_once ($fileName);
});
?>