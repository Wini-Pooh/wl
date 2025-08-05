@php
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
@endphp

@include('partner.projects.pages._template', compact('pageConfig'))
