<?php
/**
 * Danh sách đường dẫn của website. Mỗi thành viên thêm route của module mình vào đúng khối.
 * Đường dẫn tiếng Việt không dấu để thân thiện SEO và dễ nhớ.
 *
 * @var App\Core\Router $router
 */

use App\Controllers\AccountController;
use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\BudgetController;
use App\Controllers\CategoryController;
use App\Controllers\DashboardController;
use App\Controllers\HomeController;
use App\Controllers\ReportController;
use App\Controllers\TransactionController;
use App\Controllers\WalletController;

// ---- Trang công khai (TV6) ----
$router->get('/', [HomeController::class, 'index']);
$router->get('/huong-dan', [HomeController::class, 'guide']);
$router->get('/lien-he', [HomeController::class, 'contact']);
$router->post('/lien-he', [HomeController::class, 'sendContact']);

// ---- Tài khoản (TV2) ----
$router->get('/dang-nhap', [AuthController::class, 'showLogin']);
$router->post('/dang-nhap', [AuthController::class, 'login']);
$router->get('/dang-ky', [AuthController::class, 'showRegister']);
$router->post('/dang-ky', [AuthController::class, 'register']);
$router->post('/dang-xuat', [AuthController::class, 'logout']);
$router->get('/tai-khoan', [AccountController::class, 'index']);

// ---- Tổng quan và báo cáo (TV5) ----
$router->get('/tong-quan', [DashboardController::class, 'index']);
$router->get('/bao-cao', [ReportController::class, 'index']);

// ---- Giao dịch (TV3) ----
$router->get('/giao-dich', [TransactionController::class, 'index']);
$router->get('/giao-dich/xuat-csv', [TransactionController::class, 'export']);
$router->post('/giao-dich', [TransactionController::class, 'store']);
$router->get('/giao-dich/{id}', [TransactionController::class, 'show']);
$router->post('/giao-dich/{id}/sua', [TransactionController::class, 'update']);
$router->post('/giao-dich/{id}/xoa', [TransactionController::class, 'destroy']);

// ---- Ví, danh mục, ngân sách (TV4) ----
$router->get('/vi', [WalletController::class, 'index']);
$router->post('/vi', [WalletController::class, 'store']);
$router->post('/vi/{id}/sua', [WalletController::class, 'update']);
$router->get('/danh-muc', [CategoryController::class, 'index']);
$router->post('/danh-muc', [CategoryController::class, 'store']);
$router->post('/danh-muc/{id}/sua', [CategoryController::class, 'update']);
$router->post('/danh-muc/{id}/xoa', [CategoryController::class, 'destroy']);
$router->get('/ngan-sach', [BudgetController::class, 'index']);
$router->post('/ngan-sach', [BudgetController::class, 'store']);
$router->post('/ngan-sach/{id}/xoa', [BudgetController::class, 'destroy']);

// ---- Quản trị (TV2) ----
$router->get('/admin', [AdminController::class, 'index']);
