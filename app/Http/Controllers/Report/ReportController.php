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
use Illuminate\Http\Request;

class ReportController extends Controller
{

    /**
     * The service to consume the client service
     */
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Returns the Subscriptions Report
     */
    public function report(Request $request, SubscriptionService $subscriptionService, ClientService $clientService, ProductService $productService)
    {
        $info = $subscriptionService->querySubscription($request, $clientService, $productService);
        $index = [
            "Item"                  =>"id",
            "Nro suscripción"       =>"code",
            "Fecha Inicio"          =>"date_start",
            "Fecha Fin"             =>"date_end",
            "Ciclo de Facturacion"  =>"billing_cycle",
            "Estado"                =>"status",
            "Cliente"               =>"client",
            "Producto"              =>"product",
            "Precio"                =>"unit_price",
            "Impuesto"              =>"tax",
            "Cantidad"              =>"quantity"
        ];

        $info = $this->buildReportTable($info);
        $report = (new ReportService());
        $report->indexPerSheet([$index]);
        $report->dataPerSheet([$info]);
        $report->index($index);
        $report->data($info);
        $report->totalRegisters($request->ids);
        $report->external();
        $report->totalRegisters($info);
        $report->totalSubscriptions($request->ids);
        $report->transmissionRaw();

        // Load Logo
        $user = $request->get('user')->user;
        $report->getAccountInfo($user->current_account);

        return $report->report("automatic","Suscripciones",null,null,false,1);
    }

    private function buildReportTable($info){
        $table = array();
        $info = collect($info)->recursive();
        $item = 1;
        foreach ($info as $i){
            foreach ($i['subscription_details'] as $product)
            {
                array_push($table, [
                                            'id'            => $item,
                                            'code'          => $i['code'],
                                            'date_start'    => $i['date_start'],
                                            'date_end'      => $i['date_end'],
                                            'billing_cycle' => $i['billing_cycle'],
                                            'status'        => $i['status'],
                                            'client'        => $i['client']['commerce_name'],
                                            'unit_price'    => $product['unit_price'],
                                            'tax'           => $product['tax'],
                                            'quantity'      => $product['quantity'],
                                            'product'       => $product['product']
                                           ]);
                $item++;
            }
        }

        return $table;
    }
}
