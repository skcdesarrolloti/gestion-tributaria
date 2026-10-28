<?php require __DIR__.'/movements.php'; ?>
<section id="documento" class="bg-surface-container-lowest border border-surface-container rounded-xl p-4">
<h2 class="text-headline-md font-semibold"><?= e($selected['banco'].' · Cuenta terminada en '.$selected['cuenta'].' · '.\App\Services\ExtractoData::MONTHS[(int)$selected['mes']].' '.$year) ?></h2>
<p><?= e($selected['titulo']) ?> · Cargado <?= e($selected['created_at']) ?></p>
<?php $review=\App\Services\ExtractoData::reading($selected)['titular_confirmado']??null; ?>
<p><?= $review===true?'Revisión visual del titular, año y cuenta: confirmada al subir.':($review===false?'Tu revisión visual del titular, año y cuenta está pendiente. Puedes consultar el PDF abajo.':'') ?></p>
<p>Consulta el PDF aquí. Si abriste el documento protegido al subirlo, se muestra su copia privada de lectura. El original también se conserva; la clave no se guarda.</p>
<object class="bank-document" data="<?= e(url($config,'extractos/documento').'&documento='.(int)$selected['id']) ?>" type="application/pdf" aria-label="Extracto bancario guardado"><p>Este navegador no permite mostrar el PDF integrado. Usa un navegador con visor PDF para consultarlo aquí.</p></object>
</section>
