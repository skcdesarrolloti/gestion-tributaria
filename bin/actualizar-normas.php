<?php
declare(strict_types=1);
if (PHP_SAPI!=='cli') { http_response_code(404);exit; }
// Herramienta local; no se ejecuta en solicitudes web ni modifica reglas de cálculo.
require dirname(__DIR__).'/bootstrap/autoload.php';
use App\Services\{NormaCatalog,NormaExtractor};
date_default_timezone_set('America/Bogota');
$directory=dirname(__DIR__).'/resources/normas';if (!is_dir($directory)) { mkdir($directory,0755,true); }
$all=NormaCatalog::all();$generated=[];
foreach (NormaCatalog::DOCUMENTS as $key=>[$label,$document]) {
    $url='https://normograma.dian.gov.co/dian/compilacion/docs/'.$document;
    $handle=curl_init($url);curl_setopt_array($handle,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>10,CURLOPT_TIMEOUT=>40,
        CURLOPT_FOLLOWLOCATION=>false,CURLOPT_PROTOCOLS=>CURLPROTO_HTTPS,CURLOPT_MAXFILESIZE=>10000000]);
    $html=curl_exec($handle);$status=curl_getinfo($handle,CURLINFO_HTTP_CODE);curl_close($handle);
    if (!is_string($html) || $status!==200 || strlen($html)>10000000) { throw new RuntimeException('No se pudo consultar '.$label); }
    $snapshot=['source'=>$url,'reviewed'=>date('Y-m-d'),'source_sha256'=>hash('sha256',$html),'articles'=>[]];
    foreach ($all as $reference) { if ($reference['document']===$key) { $snapshot['articles'][$reference['article']]=NormaExtractor::article($html,$reference['article']); } }
    $generated[$key]=json_encode($snapshot,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR)."\n";
    echo $label.': '.count($snapshot['articles'])." artículos verificados.\n";
}
// Solo reemplaza después de poder generar todos los documentos.
foreach ($generated as $key=>$json) { file_put_contents($directory.'/'.$key.'.json',$json); }
echo "Textos generados. Revisar contenido y diferencias antes de publicar.\n";
