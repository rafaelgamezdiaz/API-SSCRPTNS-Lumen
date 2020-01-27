<?php
/**
 * Created by PhpStorm.
 * User: develop
 * Date: 13/03/19
 * Time: 02:40 PM
 */

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Services\ClientService;
use App\Services\ProductService;
use App\Services\ReportService;
use App\Services\SubscriptionService;
use function foo\func;
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

    public function report(Request $request, SubscriptionService $subscriptionService, ClientService $clientService, ProductService $productService)
    {
        $info = $subscriptionService->querySubscription($request, $clientService, $productService);
        $index = [
            "Id"                    =>"id",
            "Code"                  =>"code",
            "Fecha Inicio"          =>"date_start",
            "Fecha Fin"             =>"date_end",
            "Ciclo de Facturacion"  =>"billing_cycle",
            "Estado"                =>"status",
            "Cliente"               =>"client",
            "Product"               =>"product",
            "Price"                 =>"sale_price"
        ];
        $info = $this->buildReportTable($info);
        $report = (new ReportService());
        $report->indexPerSheet([$index]);
        $report->dataPerSheet([$info]);
        $report->index($index);
        $report->data($info);
        $report->external();
        return $report->report("automatic","Report",null,null,false,1);
    }

    private function buildReportTable($info){
        $table = array();
        $info = collect($info)->recursive();
        foreach ($info as $i){

            foreach ($i['subscription_details'] as $product)
            {
                array_push($table, [
                                            'id'            => $i['id'],
                                            'code'          => $i['code'],
                                            'date_start'    => $i['date_start'],
                                            'date_end'      => $i['date_end'],
                                            'billing_cycle' => $i['billing_cycle'],
                                            'status'        => $i['status'],
                                            'client'        => $i['client']['name'].' '.$i['client']['last_name'],
                                            'product'       => $product['product'][0]['name'],
                                            'sale_price'    => $product['product'][0]['sale_price']
                                           ]);
            }
        }
        return $table;
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


    /**
     * @param Request $request
     * @return \Dompdf\Dompdf|\Illuminate\Http\JsonResponse|string|null
     */
    /*
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
    }*/
}
