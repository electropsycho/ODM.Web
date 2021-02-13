@extends('reports.ReportTemplate')

@section('title', $exam->code.' Kodlu Sınav')
{{--@section('memLogo', $memLogo)--}}
{{--@section('odmLogo', $odmLogo)--}}
@section('city', mb_strtoupper(str_replace("i", "İ", $settings->city)))
{{--@section('reportHeader', $exam->code.' Kodlu Sınav Ayrıntıları Raporu')--}}

@section('content')
    <div class="content">
        <h3>{{$exam->code}} {{$exam->title}} Raporu</h3>
        <table width="100%">
            <tbody>
            <tr>
                <td align="left"><b>Başlık:</b> {{$exam->title}}</td>
                <td align="left"><b>Oluşturan:</b> {{$exam->creator}}</td>
                <td align="left"><b>Planlanan Tarih:</b> {{date("d.m.Y H:i", strtotime($exam->planned_date))}}</td>
            </tr>
            <tr>
                <td align="left"><b>Tür/Amaç:</b> {{$exam->purpose}}</td>
                <td align="left"><b>Kodu:</b> {{$exam->code}}</td>
                <td align="left"><b>Sınıf Seviyesi:</b> {{$exam->class_level}}. Sınıf</td>
            </tr>
            </tbody>
        </table>
        <table width="100%">
            <thead style="background-color: lightgray;">
            <tr>
                <th class="bottomBorder" align="left">Sıra</th>
                <th class="bottomBorder" align="center">Dosya Adı</th>
                <th class="bottomBorder" align="center">Tasarım?</th>
                <th class="bottomBorder" align="center">Soru Sahibi</th>
                <th class="bottomBorder" align="center">D. Cvp</th>
                <th class="bottomBorder" align="center">Kazanım</th>
                <th class="bottomBorder" align="center">Zorluk</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($questions as $question)
                <tr>
                    <td class="bottomBorder" align="left">{{$loop->iteration}}.</td>
                    <td class="bottomBorder" align="center">{{$question->file_name}}</td>
                    <td class="bottomBorder" align="center">{{$question->idr_text}}</td>
                    <td class="bottomBorder" align="center">{{$question->creator}}</td>
                    <td class="bottomBorder" align="center">{{$question->correct_answer}}</td>
                    <td class="bottomBorder" align="left"
                        style="text-align: justify">{{$question->learning_outcome}}</td>
                    <td class="bottomBorder" align="center">{{$question->difficulty}}</td>
                </tr>
            @endforeach
            </tbody>

            <tfoot>
            <tr>
                <td colspan="6" align="right">Tasarım İhtiyacı Olan Soru Sayısı:</td>
                <td align="center">{{$questions->filter(function ($item) { return $item->is_design_required;})->count()}}</td>
            </tr>
            <tr>
                <td colspan="6" align="right">Tasarım İhtiyacı Olmayan Soru Sayısı:</td>
                <td align="center">{{$questions->filter(function ($item) { return !$item->is_design_required; })->count()}}</td>
            </tr>
            <tr>
                <td colspan="6" align="right">Toplam Soru Sayısı:</td>
                <td align="center">{{count($questions)}}</td>
            </tr>
            </tfoot>
        </table>
    </div>
@endsection
