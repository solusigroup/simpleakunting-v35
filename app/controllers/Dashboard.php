<?php

class Dashboard extends Controller {
    public function __construct() {
        parent::__construct();
        if (!Auth::isLoggedIn()) {
            header('Location: ' . BASEURL . '/login');
            exit;
        }
    }

    public function index() {
        $user = Auth::user();
        if (!$user) {
            header('Location: ' . BASEURL . '/login');
            exit;
        }

        $dashboardModel = $this->model('Dashboard');
        $currentTenantId = $this->tenantId();
        $isCentralUser = (Auth::hasRole('Superadmin') || Auth::hasRole('Penyelia Wilayah')) && $currentTenantId === null;
        
        if ($isCentralUser) {
            // CENTRAL DASHBOARD LOGIC (Melihat Aggregate / Monitoring Kluster)
            $data['judul'] = 'Central Dashboard';
            
            if (Auth::isPenyeliaWilayah() && Auth::getKlusterWilayahId()) {
                $summary = $dashboardModel->getCentralSummaryByKluster(Auth::getKlusterWilayahId());
                $data['tenants'] = $this->model('Tenants')->getTenantsByKluster(Auth::getKlusterWilayahId());
            } else {
                $summary = $dashboardModel->getCentralSummary();
                $data['tenants'] = $this->model('Tenants')->getAllTenants();
            }
            
            $data['summary'] = is_array($summary) ? $summary : [];
            $data['tenants'] = is_array($data['tenants']) ? $data['tenants'] : [];

            // Tren agregat
            $trendData = $dashboardModel->getSalesPurchasesTrend(null);
            $data['chart_trend'] = $this->_prepareTrendData($trendData);

            $this->view('templates/header', $data);
            $this->view('dashboard/central', $data);
            $this->view('templates/footer');
        } else {
            // TENANT DASHBOARD LOGIC (User normal atau Superadmin yang sedang memantau tenant)
            $data['judul'] = 'Dashboard';
            if (isset($user['impersonating'])) {
                $data['judul'] .= ' - Monitoring ' . ($user['tenant_name'] ?? '');
            }
            
            $summary = $dashboardModel->getSummary($currentTenantId);
            $data['summary'] = is_array($summary) ? $summary : [];

            $trendData = $dashboardModel->getSalesPurchasesTrend($currentTenantId);
            $data['chart_trend'] = $this->_prepareTrendData($trendData);

            $this->view('templates/header', $data);
            $this->view('dashboard/index', $data);
            $this->view('templates/footer');
        }
    }

    private function _prepareTrendData($trendData) {
        $labels = [];
        $salesData = [];
        $purchasesData = [];
        if (is_array($trendData)) {
            foreach($trendData as $row) {
                $periode = !empty($row['periode']) ? $row['periode'] . '-01' : date('Y-m-d');
                $labels[] = date('M Y', strtotime($periode));
                $salesData[] = $row['total_penjualan'] ?? 0;
                $purchasesData[] = $row['total_pembelian'] ?? 0;
            }
        }
        return [
            'labels' => json_encode($labels),
            'sales' => json_encode($salesData),
            'purchases' => json_encode($purchasesData)
        ];
    }
}
