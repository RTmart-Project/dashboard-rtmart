<?php

namespace App\Services;

use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RTSalesService
{
  public function salesLists()
  {
    $sql = DB::table('ms_sales')
      ->join('ms_team_name', 'ms_team_name.TeamCode', 'ms_sales.Team')
      ->leftJoin('ms_sales_work_status', 'ms_sales_work_status.SalesWorkStatusID', 'ms_sales.SalesWorkStatus')
      ->leftJoin('ms_sales_product_group', 'ms_sales_product_group.SalesCode', 'ms_sales.SalesCode')
      ->leftJoin('ms_product_group', 'ms_product_group.ProductGroupID', 'ms_sales_product_group.ProductGroupID')
      ->selectRaw("
        ms_sales.SalesName,
        ms_sales.SalesCode,
        ms_sales.SalesLevel,
        ms_sales.Email,
        ms_sales.PhoneNumber,
        ms_sales.Password,
        ms_sales.IsActive,
        ms_sales_work_status.SalesWorkStatusName,
        GROUP_CONCAT(ms_product_group.ProductGroupName) AS ProductGroupName,
        CONCAT(ms_sales.TeamBy, ' ', ANY_VALUE(ms_team_name.TeamName)) AS Team
      ")
      ->groupBy('ms_sales.SalesCode');

    return $sql;
  }

  public function storeLists()
  {
    $sql = DB::table('ms_store')
      ->leftJoin('ms_sales', 'ms_sales.SalesCode', 'ms_store.SalesCode')
      ->where('ms_store.IsActive', 1)
      ->select('ms_store.StoreID', 'ms_store.StoreName', 'ms_store.OwnerName', 'ms_store.PhoneNumber', 'ms_store.StoreAddress', 'ms_store.Grade', 'ms_store.MerchantID', 'ms_store.CreatedDate', 'ms_store.StoreType', 'ms_store.SalesCode', 'ms_sales.SalesName');

    return $sql;
  }

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
      ->where('msales.IsActive', 1)
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

    if (Auth::user()->Depo != "ALL") {
      $depoUser = Auth::user()->Depo;
      $sql->where('msales.Depo', '=', $depoUser);
    }

    return $sql;
  }

  public function surveyReportData($fromDate = null, $toDate = null)
  {
    $startDate = new DateTime($fromDate) ?? new DateTime();
    $endDate = new DateTime($toDate) ?? new DateTime();
    $startDateFormat = $startDate->format('Y-m-d');
    $endDateFormat = $endDate->format('Y-m-d');

    // if (Auth::user()->Depo != "ALL") {
    //   $depoUser = Auth::user()->Depo;
    // } else {
    //   $depoUser = "";
    // }

    $sql = DB::table('ms_visit_survey')
      ->select('ms_visit_survey.VisitSurveyID', 'ms_visit_survey.CreatedDate', 'ms_sales.SalesCode', 'ms_sales.SalesName', 'ms_visit_plan_result.StoreID', 'ms_store.StoreName', 'ms_store.PhoneNumber', 'ms_product.ProductName', 'ms_visit_survey.PurchasePrice', 'ms_visit_survey.SellingPrice', 'ms_visit_survey.Supplier')
      ->leftJoin('ms_visit_plan_result', 'ms_visit_survey.VisitResultID', 'ms_visit_plan_result.VisitResultID')
      ->join('ms_product', 'ms_visit_survey.ProductID', 'ms_product.ProductID')
      ->join('ms_sales', function ($join) {
        $join->on('ms_visit_plan_result.SalesCode', 'ms_sales.SalesCode');
        if (Auth::user()->Depo != "ALL") {
          $depoUser = Auth::user()->Depo;
          $join->where('ms_sales.Depo', '=', $depoUser);
        }
      })
      ->join('ms_store', 'ms_visit_plan_result.StoreID', 'ms_store.StoreID')
      ->where('ms_sales.IsActive', 1)
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

  public function generateStoreID()
  {
    $max = DB::table('ms_store')
      ->max('StoreID');

    if ($max == null) {
      $newStoreID = "STR-000001";
    } else {
      $maxStoreID = substr($max, -6);
      $newNumberStoreID = $maxStoreID + 1;
      $newStoreID = "STR-" . str_pad($newNumberStoreID, 6, '0', STR_PAD_LEFT);
    }

    return $newStoreID;
  }
}
