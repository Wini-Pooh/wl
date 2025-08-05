@php
$pageConfig = [
    'pageName' => 'СТРАНИЦА ДИЗАЙНА',
    'pageNameLower' => 'страницы дизайна',
    'pageNameFormatted' => 'Страница дизайна',
    'initIcon' => '🎨',
    'handlerIcon' => '🎨',
    'cssFile' => 'design-standard.css',
    'tabFile' => 'design',
    'modalType' => 'design',
    'modalId' => 'uploadDesignModal',
    'fileInputId' => 'designFiles',
    'fileIcon' => 'bi-paint-bucket',
    'tabContentId' => 'design-tab-content',
    'initFunction' => 'initDesignHandlers',
    'itemIdParam' => 'designId',
    'itemNameAccusative' => 'файл дизайна',
    'deleteRoute' => route('partner.projects.design.destroy', [$project, '__ID__']),
    'viewRoute' => route('partner.projects.design.view', [$project, '__ID__']),
    'openFunction' => 'openDesignView'
];
@endphp

@include('partner.projects.pages._template', compact('pageConfig'))
