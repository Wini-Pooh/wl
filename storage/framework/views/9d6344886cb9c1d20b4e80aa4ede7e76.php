<?php
$pageConfig = [
    'pageName' => 'СТРАНИЦА ДОКУМЕНТОВ',
    'pageNameLower' => 'страницы документов',
    'pageNameFormatted' => 'Страница документов',
    'initIcon' => '📄',
    'handlerIcon' => '📄',
    'cssFile' => 'documents-standard.css',
    'tabFile' => 'documents',
    'modalType' => 'document',
    'modalId' => 'uploadDocumentModal',
    'fileInputId' => 'documentFiles',
    'fileIcon' => 'bi-file-earmark-text',
    'tabContentId' => 'documents-tab-content',
    'initFunction' => 'initDocumentsHandlers',
    'itemIdParam' => 'documentId',
    'itemNameAccusative' => 'документ',
    'deleteRoute' => route('partner.projects.documents.destroy', [$project, '__ID__']),
    'viewRoute' => route('partner.projects.documents.show', [$project, '__ID__']),
    'openFunction' => 'openDocument'
];
?>

<?php echo $__env->make('partner.projects.pages._template', compact('pageConfig'), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/projects/pages/documents.blade.php ENDPATH**/ ?>