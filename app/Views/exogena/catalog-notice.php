<section class="bg-primary-container rounded-2xl p-space-xl space-y-space-sm">
    <h2 class="text-headline-sm font-semibold">Referencia versión <?= e($schema['version']) ?> · Año de preparación <?= e($schema['year']) ?></h2>
    <p class="text-body-md"><?= count($schema['fields']) ?> campos de captura basados en el anexo DIAN. Los campos de un registro nuevo están vacíos; cero debe registrarse expresamente.</p>
    <p class="text-body-md">Se preparan borradores por cliente. La carga mensual, sus controles de duplicados y la conciliación anual se desarrollarán al recibir los archivos. No se genera ni envía XML en esta etapa.</p>
    <?php if (in_array($schema['code'],['1008','1009','5251','5252'],true)): ?><p class="text-body-md">Este formato reporta un saldo al corte: los saldos de varios meses no se suman como movimientos.</p><?php endif; ?>
    <?php if ($schema['group']==='contratos'): ?><p class="text-body-md">Esta referencia incluye número de contrato y NIESPJ. Para tipos de contrato 3, 4 o 5 se exige NIESPJ. Revisar el contrato y su soporte antes de reportar.</p><?php endif; ?>
    <details>
        <summary class="text-body-md underline">Fundamento, versión y alcance</summary>
        <p class="text-body-md mt-2"><?= e($schema['norm']) ?></p>
        <p class="text-body-md mt-2">Los códigos requieren validación con los catálogos del período. La captura verifica estructura y condiciones básicas; no certifica obligación, tratamiento fiscal, agrupación ni ausencia de duplicados de presentación.</p>
        <p class="text-body-md mt-2"><a class="text-secondary underline" href="<?= e($schema['source']) ?>" target="_blank" rel="noopener noreferrer">Anexo oficial DIAN · Páginas <?= e($schema['pages']) ?> del PDF</a> · <a class="text-secondary underline" href="https://www.dian.gov.co/impuestos/sociedades/ExogenaTributaria/Prevalidadores/Paginas/default.aspx" target="_blank" rel="noopener noreferrer">Prevalidadores DIAN</a></p>
    </details>
</section>
