<?php

class Dashboard extends Controller {
    public function index() {
        $user = Auth::user();
        $dashboardModel = $this->model('Dashboard');
        $currentTenantId = $this->tenantId();
        
        if ($user['role'] === 'Superadmin' && $currentTenantId === null) {
            // CENTRAL DASHBOARD LOGIC (Melihat Aggregate)
            $data['judul'] = 'Central Dashboard';
            $data['summary'] = $dashboardModel->getCentralSummary();
            $data['tenants'] = $this->model('Tenants')->getAllTenants();
            
            // Tren agregat
            $trendData = $dashboardModel->getSalesPurchasesTrend(null);
            $data['chart_trend'] = $this->_prepareTrendData($trendData);

            $this->view('templates/header', $data);
            $this->view('dashboard/central', $data);
            $this->view('templates/footer');
        } else {
            // TENANT DASHBOARD LOGIC (Bisa user normal atau Superadmin yang sedang memantau tenant)
            $data['judul'] = 'Dashboard';
            if (isset($user['impersonating'])) {
                $data['judul'] .= ' - Monitoring ' . ($user['tenant_name'] ?? '');
            }
            
            $data['summary'] = $dashboardModel->getSummary($currentTenantId);
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
        foreach($trendData as $row) {
            $labels[] = date('M Y', strtotime($row['periode'] . '-01'));
            $salesData[] = $row['total_penjualan'];
            $purchasesData[] = $row['total_pembelian'];
        }
        return [
            'labels' => json_encode($labels),
            'sales' => json_encode($salesData),
            'purchases' => json_encode($purchasesData)
        ];
    }
}
