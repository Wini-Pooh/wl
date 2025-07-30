<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Response;
use Illuminate\Contracts\View\View as ViewContract;

class ProjectModalController extends Controller
{
    /**
     * Получить HTML модального окна для указанного типа
     *
     * @param Request $request
     * @param int $projectId
     * @param string $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function getModal(Request $request, $projectId, $type)
    {
        // Найдем проект по ID
        $project = Project::findOrFail($projectId);
        
        // Получаем дополнительные параметры из запроса
        $params = $request->all();
        
        // Определяем, какое модальное окно нужно вернуть
        $view = '';
        
        switch ($type) {
            case 'photo':
                $view = view('partner.projects.tabs.modals.photo-modal', compact('project', 'params'))->render();
                break;
            case 'document':
                $view = view('partner.projects.tabs.modals.document-modal', compact('project', 'params'))->render();
                break;
            case 'design':
                $view = view('partner.projects.tabs.modals.design-modal', compact('project', 'params'))->render();
                break;
            case 'scheme':
                $view = view('partner.projects.tabs.modals.scheme-modal', compact('project', 'params'))->render();
                break;
            case 'work-add':
                $view = view('partner.projects.tabs.modals.work-modal', compact('project', 'params'))->render();
                break;
            case 'material-add':
                $view = view('partner.projects.tabs.modals.material-modal', compact('project', 'params'))->render();
                break;
            case 'transport-add':
                $view = view('partner.projects.tabs.modals.transport-modal', compact('project', 'params'))->render();
                break;
            case 'stage-add':
            case 'stage-edit':
                $view = view('partner.projects.tabs.modals.stage-modal', compact('project', 'params'))->render();
                break;
            case 'event-add':
            case 'event-edit':
                $view = view('partner.projects.tabs.modals.event-modal', compact('project', 'params'))->render();
                break;
            case 'work-edit':
                $view = view('partner.projects.tabs.modals.work-modal', compact('project', 'params'))->render();
                break;
            case 'material-edit':
                $view = view('partner.projects.tabs.modals.material-modal', compact('project', 'params'))->render();
                break;
            case 'transport-edit':
                $view = view('partner.projects.tabs.modals.transport-modal', compact('project', 'params'))->render();
                break;
            default:
                return response()->json([
                    'success' => false, 
                    'error' => 'Неизвестный тип модального окна: ' . $type
                ], 400);
        }
        
        return response()->json([
            'success' => true,
            'html' => $view
        ]);
    }
}
