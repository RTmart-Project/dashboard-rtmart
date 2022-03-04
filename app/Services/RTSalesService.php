<?php

namespace App\Services;

use DateTime;
use Illuminate\Support\Facades\DB;

class RTSalesService
{

  public function callReportData($fromDate = null, $toDate = null)
  {
    $startDate = new DateTime($fromDate) ?? new DateTime();
    $endDate = new DateTime($toDate) ?? new DateTime();
    $startDateFormat = $startDate->format('Y-m-d');
    $endDateFormat = $endDate->format('Y-m-d');

    $rangeDayName = array();
    for ($date = $startDate; $date <= $endDate; $date->modify('+1 day')) {
      array_push($rangeDayName, $date->format('l'));
    }
    $implodeRangeDayName = "'" . implode("','", $rangeDayName) . "'";

    $sql = DB::table('ms_sales AS msales')
      ->leftJoin('ms_visit_plan_result AS VisitResult', function ($join) use ($startDateFormat, $endDateFormat) {
        $join->on('VisitResult.SalesCode', 'msales.SalesCode');
        $join->whereBetween('VisitResult.ActualVisitDate', [$startDateFormat, $endDateFormat]);
      })
      ->leftJoin('ms_team_name', 'ms_team_name.TeamCode', 'msales.Team')
      ->selectRaw("
                ANY_VALUE(DATE_FORMAT('$startDateFormat', '%d-%b-%Y')) as StartDate,
                ANY_VALUE(DATE_FORMAT('$endDateFormat', '%d-%b-%Y')) as EndDate,
                msales.SalesCode,
                msales.SalesName,
                msales.TeamBy,
                msales.Team,
                ANY_VALUE(ms_team_name.TeamName) AS NamaTeam,
                (SELECT COUNT(ms_visit_plan.VisitPlanID) 
                    FROM ms_visit_plan
                    WHERE ms_visit_plan.SalesCode = msales.SalesCode
                    AND ms_visit_plan.VisitDayName IN ($implodeRangeDayName)
                ) AS TargetCall,
                COUNT(VisitResult.VisitResultID) AS Actual,
                IFNULL(SUM(TIMESTAMPDIFF(MINUTE, VisitResult.SigninTime, VisitResult.SignoutTime)), 0) AS Duration,
                IFNULL(MIN(VisitResult.SigninTime), 0) AS CheckIn,
                IFNULL(MAX(VisitResult.SignoutTime), 0) AS CheckOut,
                (SELECT IFNULL(SUM(tx_merchant_order.NettPrice), 0)
                    FROM tx_merchant_order
                    JOIN ms_merchant_account ON ms_merchant_account.MerchantID = tx_merchant_order.MerchantID
                    WHERE ms_merchant_account.ReferralCode = msales.SalesCode
                    AND (tx_merchant_order.CreatedDate BETWEEN '$startDateFormat' AND '$endDateFormat')
                ) AS Omzet
            ")
      ->groupBy('msales.SalesCode', 'msales.SalesName', 'msales.TeamBy', 'msales.Team');

    return $sql;
  }

  public function surveyReportData($fromDate = null, $toDate = null)
  {
    $startDate = new DateTime($fromDate) ?? new DateTime();
    $endDate = new DateTime($toDate) ?? new DateTime();
    $startDateFormat = $startDate->format('Y-m-d');
    $endDateFormat = $endDate->format('Y-m-d');

    $sql = DB::table('ms_visit_survey')
      ->select('ms_visit_survey.VisitSurveyID', 'ms_visit_survey.CreatedDate', 'ms_sales.SalesCode', 'ms_sales.SalesName', 'ms_visit_plan_result.StoreID', 'ms_store.StoreName', 'ms_store.PhoneNumber', 'ms_product.ProductName', 'ms_visit_survey.PurchasePrice', 'ms_visit_survey.SellingPrice', 'ms_visit_survey.Supplier')
      ->leftJoin('ms_visit_plan_result', 'ms_visit_survey.VisitResultID', 'ms_visit_plan_result.VisitResultID')
      ->join('ms_product', 'ms_visit_survey.ProductID', 'ms_product.ProductID')
      ->join('ms_sales', 'ms_visit_plan_result.SalesCode', 'ms_sales.SalesCode')
      ->join('ms_store', 'ms_visit_plan_result.StoreID', 'ms_store.StoreID')
      ->whereDate('ms_visit_survey.CreatedDate', '>=', $startDateFormat)
      ->whereDate('ms_visit_survey.CreatedDate', '<=', $endDateFormat)
      ->get();

    foreach ($sql as $key => $value) {
      $surveyPhoto = DB::table('ms_visit_survey_photo')
        ->select('TypePhoto', 'UrlPhoto')
        ->where('VisitSurveyID', $value->VisitSurveyID)
        ->get()->toJson();

      $value->SurveyPhoto = $surveyPhoto;
    }

    return $sql;
  }
}