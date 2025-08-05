@php
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
    'viewRoute' => route('partner.projects.schemes.view', [$project, '__ID__']),
    'openFunction' => 'openSchemeView'
];
@endphp

@include('partner.projects.pages._template', compact('pageConfig'))
