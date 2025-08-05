@extends('layouts.app')

@section('content')
<div class="alert alert-success">
    <h4>Тест структуры layout</h4>
    <p>Этот контент должен появиться внутри content-container, а не выше шапки.</p>
    <small>Время создания: {{ now() }}</small>
</div>
@endsection
