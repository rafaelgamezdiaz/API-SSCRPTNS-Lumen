<?php
/**
 * Created by PhpStorm.
 * User: develop
 * Date: 13/03/19
 * Time: 02:40 PM
 */

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{

    /**
     * The service to consume the client service
     * @var
     */
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * @param Request $request
     * @return \Dompdf\Dompdf|\Illuminate\Http\JsonResponse|string|null
     */
    public function automatic(Request $request)
    {
        $index= [$request->index];
        $info = $this->sortByDate([$request->data]);
        $name_date = $this->dateToStr($info[0]);
        $report = (new ReportService());
        $name = $request->has('name') ? $request->input('name') : "Report_".$name_date;
        $report->indexPerSheet($index);
        $report->dataPerSheet($info);
        $report->data($request->data);
        $report->index($request->index);
        $report->external();
        $report->transmissionRaw();
        return $report->report("automatic",$name,"",null,false,1);
    }

    private function dateToStr($info){
        if (count($info) > 0) {
            $dateIni = $info[0]['date_start'];
            $dateEnd = $info[count($info)-1]['date_end'];
        }
        return $this->formatDateToString($dateIni, $dateEnd);
    }

    private function formatDateToString($dateIni, $dateEnd){
        $date_ini = explode(' ',$dateIni)[0];
        $date_ini = implode(explode('-',$date_ini));
        $date_end = explode(' ',$dateEnd)[0];
        $date_end = implode(explode('-',$date_end));
        return $date_ini != $date_end ? '_'.$date_ini.'_'.$date_end : '_'.$date_ini;
    }


    private function sortByDate($info){
        $infoSorted = collect($info[0])->sortBy('date_start')->values()->all();
        return [$infoSorted];
    }
}
