<?php
declare(strict_types=1);

// PDF mínimo construido en memoria con datos exclusivamente ficticios.
function rutFixturePdf(string $nit='12345678'): string
{
    $dv=App\Services\RutPdfForm::digit($nit); $commands=[];
    $text=static function (float $x,float $y,string $value) use (&$commands): void {
        $value=str_replace(['\\','(',')'],['\\\\','\\(','\\)'],$value);
        $commands[]='BT /F1 8 Tf 1 0 0 1 '.$x.' '.$y.' Tm ('.$value.') Tj ET';
    };
    $text(20,760,'Numero de formulario - Identificacion Tributaria - Responsabilidades - Formulario 001');
    $text(20,745,str_repeat('Documento de prueba ficticio sin validez. ',8));
    $text(515,704.47,'99999999999'); $text(28,603.53,$nit); $text(192.5,603.53,$dv);
    $text(205,607.47,'Seccional de prueba'); $text(285,569.53,'13'); $text(337,569.53,$nit);
    $text(27,521.47,'EJEMPLO'); $text(155,521.47,'FICTICIO'); $text(282,521.47,'PERSONA');
    $text(27,545.47,'COLOMBIA 169'); $text(218,545.47,'Departamento 13'); $text(407,545.47,'Ciudad 001');
    $text(27,437.47,'COLOMBIA 169'); $text(201,437.47,'Departamento 13'); $text(393,437.47,'Ciudad 001');
    $text(27,412.5,'Direccion ficticia 1'); $text(106,401.5,'prueba@example.invalid'); $text(309,387.5,'3000000000');
    $text(32.9,329.5,'0010'); $text(79.4,329.5,'20200101'); $text(64.8,291.5,'05'); $text(85,291.5,'22');
    $text(452,103.5,'2022-08-08 / 04:00PM'); $text(20,10,'Fecha generacion documento PDF: '.date('d-m-Y'));
    $stream=implode("\n",$commands);
    $objects=['<< /Type /Catalog /Pages 2 0 R >>','<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
        '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 5 0 R >> >> /Contents 4 0 R >>',
        '<< /Length '.strlen($stream)." >>\nstream\n".$stream."\nendstream",
        '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>'];
    $pdf="%PDF-1.4\n"; $offsets=[0];
    foreach ($objects as $i=>$object) { $offsets[]=strlen($pdf); $pdf.=($i+1)." 0 obj\n".$object."\nendobj\n"; }
    $xref=strlen($pdf); $pdf.="xref\n0 6\n0000000000 65535 f \n";
    foreach (array_slice($offsets,1) as $offset) { $pdf.=sprintf('%010d 00000 n ',$offset)."\n"; }
    return $pdf."trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n".$xref."\n%%EOF\n";
}
