<?php
$pageConfig = [
    'pageName' => 'СТРАНИЦА СХЕМ',
    'pageNameLower' => 'страницы схем',
    'pageNameFormatted' => 'Страница схем',
    'initIcon' => '📋',
    'handlerIcon' => '📋',
    'cssFile' => 'schemes-standard.css',
    'tabFile' => 'schemes',
    'modalFile' => 'scheme-modal',
    'modalType' => 'scheme',
    'modalId' => 'uploadSchemeModal',
    'fileInputId' => 'schemeFiles',
    'fileIcon' => 'bi-diagram-3',
    'tabContentId' => 'schemes-tab-content',
    'initFunction' => 'initSchemesHandlers',
    'itemIdParam' => 'schemeId',
    'itemNameAccusative' => 'схему',
    'deleteRoute' => route('partner.projects.schemes.destroy', [$project, '__ID__']),
    'viewRoute' => route('partner.projects.schemes.show', [$project, '__ID__']),
    'openFunction' => 'openSchemeView'
];
?>

<?php echo $__env->make('partner.projects.pages._template', compact('pageConfig'), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/projects/pages/schemes.blade.php ENDPATH**/ ?>