<?php

namespace App\Http\Controllers;

use App\Services\MailChimpService;
use App\Services\ReportingService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(protected ReportingService $reportingService, protected MailChimpService $mailChimpService) {}

    public function generateContactInteractionsReport(Request $request): Factory|View
    {
        $data = $this->reportingService->getContactInteractionsData($request->all());

        return view('reports.contact-interactions', ['data' => $data]);
    }

    public function generateSalesPipelineReport(Request $request): Factory|View
    {
        $data = $this->reportingService->getSalesPipelineData($request->all());

        return view('reports.sales-pipeline', ['data' => $data]);
    }

    public function generateCustomerEngagementReport(Request $request): Factory|View
    {
        $data = $this->reportingService->getCustomerEngagementData($request->all());

        return view('reports.customer-engagement', ['data' => $data]);
    }

    public function generateABTestResultsReport(Request $request): Factory|View
    {
        $campaignId = $request->input('campaign_id');
        $data = $this->mailChimpService->getABTestResults($campaignId);

        return view('reports.ab-test-results', ['data' => $data]);
    }

    public function generateEmailCampaignReport(Request $request): Factory|View
    {
        $campaignId = $request->input('campaign_id');
        $data = $this->mailChimpService->getCampaignReport($campaignId);

        return view('reports.email-campaign-performance', ['data' => $data]);
    }
}
