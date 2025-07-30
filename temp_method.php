/**
     * Получение данных проекта для автозаполнения документов
     */
    public function getProjectData($projectId)
    {
        $user = Auth::user();
        
        // Находим проект и загружаем связанные данные
        $project = \App\Models\Project::with('estimates')->findOrFail($projectId);
        
        // Проверяем, что пользователь имеет доступ к проекту
        if (!$user->hasRole('admin') && 
            !($user->hasRole('manager') && $project->manager_id == $user->id) && 
            !($user->hasRole('partner') && $project->partner_id == $user->id)) {
            return response()->json(['error' => 'Нет доступа к данному проекту'], 403);
        }
        
        // Формируем адрес объекта
        $address = [];
        if (!empty($project->object_city)) $address[] = $project->object_city;
        if (!empty($project->object_street)) $address[] = $project->object_street;
        if (!empty($project->object_house)) $address[] = $project->object_house;
        if (!empty($project->apartment_number)) $address[] = 'кв. ' . $project->apartment_number;
        $projectAddress = implode(', ', $address);
        
        // Собираем информацию для автозаполнения
        $data = [
            'project' => [
                'id' => $project->id,
                'address' => $projectAddress,
                'passport_series' => $project->passport_series,
                'passport_number' => $project->passport_number,
                'passport_issued_by' => $project->passport_issued_by,
                'passport_date' => $project->passport_issued_date,
                'passport_department_code' => $project->passport_department_code,
                'total_area' => $project->object_area,
                'contract_number' => $project->contract_number,
            ],
            'client' => [
                'name' => $project->client_last_name . ' ' . $project->client_first_name,
                'phone' => $project->client_phone,
                'email' => $project->client_email,
            ],
            'estimates' => $project->estimates->map(function($estimate) {
                return [
                    'id' => $estimate->id,
                    'total_cost' => $estimate->total_cost,
                    'materials_cost' => $estimate->materials_cost,
                    'work_cost' => $estimate->work_cost,
                ];
            }),
        ];
        
        return response()->json($data);
    }
