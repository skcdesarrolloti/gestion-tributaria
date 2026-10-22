<details class="border border-surface-container rounded-xl p-4">
    <summary class="font-semibold">Soporte y notas</summary>
    <div class="profile-grid mt-2">
        <?php
        $metadata=[
            'estado'=>['Estado de la información','select',['borrador'=>'Borrador: estoy completando','declarado'=>'Declarado por el contribuyente','documentado'=>'Revisado por mí contra los documentos']],
            'fecha'=>['Fecha de revisión','date',[]], 'fuente'=>['Documento o fuente de los datos','text',[]],
            'notas'=>['Notas, diferencias o documentos pendientes','textarea',[]],
        ];
        $actualBlock=$block; $block='control';
        foreach ($metadata as $key=>[$label,$kind,$options]):
            $definition=\App\Services\CaracterizacionSchema::field($label,$kind,$options);
            $inputId=$key; $name=$key; $value=$record[$key]; require __DIR__.'/field.php';
        endforeach; $block=$actualBlock;
        ?>
    </div>
</details>
