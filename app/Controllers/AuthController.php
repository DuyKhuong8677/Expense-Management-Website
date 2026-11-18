<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Validator;
use App\Models\User;

/**
 * Đăng ký, đăng nhập, đăng xuất — phụ trách: TV2 (feature/tai-khoan)
 * Đã có sẵn bản cơ bản để cả nhóm đăng nhập được khi làm module của mình.
 * TODO TV2 (FR-03): khóa đăng nhập 15 phút sau 5 lần sai (cột failed_attempts, locked_until đã có trong bảng users).
 */
class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            redirect('tong-quan');
        }
        $this->view('auth/login', ['pageTitle' => 'Đăng nhập'], 'public');
    }

    public function login(): void
    {
        $v = Validator::make($_POST, [
            'email'    => 'required|email',
            'password' => 'required',
        ], ['email' => 'Email', 'password' => 'Mật khẩu']);

        if ($v->fails()) {
            with_old($_POST);
            with_errors($v->errors());
            redirect('dang-nhap');
        }

        $user = User::findByEmail($_POST['email']);
        // Không nói rõ sai email hay sai mật khẩu, để kẻ gian không dò được email nào tồn tại
        if (!$user || !password_verify($_POST['password'], $user['password_hash'])) {
            with_old($_POST);
            flash('danger', 'Email hoặc mật khẩu không đúng.');
            redirect('dang-nhap');
        }
        if ((int)$user['is_locked'] === 1) {
            flash('danger', 'Tài khoản đã bị khóa. Vui lòng liên hệ quản trị viên.');
            redirect('dang-nhap');
        }

        Auth::login($user);
        flash('success', 'Chào ' . $user['full_name'] . '!');
        redirect($user['role'] === 'admin' ? 'admin' : 'tong-quan');
    }

    public function showRegister(): void
    {
        if (Auth::check()) {
            redirect('tong-quan');
        }
        $this->view('auth/register', ['pageTitle' => 'Đăng ký'], 'public');
    }

    public function register(): void
    {
        $v = Validator::make($_POST, [
            'full_name'             => 'required|max:100',
            'email'                 => 'required|email|max:191',
            'password'              => 'required|min:8|password',
            'password_confirmation' => 'required|same:password',
        ], [
            'full_name'             => 'Họ tên',
            'email'                 => 'Email',
            'password'              => 'Mật khẩu',
            'password_confirmation' => 'Mật khẩu nhập lại',
        ]);

        $errors = $v->errors();
        if (!isset($errors['email']) && User::emailExists($_POST['email'])) {
            $errors['email'] = 'Email này đã được đăng ký.';
        }
        if ($errors) {
            with_old($_POST);
            with_errors($errors);
            redirect('dang-ky');
        }

        $userId = User::register($_POST['full_name'], $_POST['email'], $_POST['password']);
        Auth::login(['id' => $userId]);
        flash('success', 'Tạo tài khoản thành công. Ví "Tiền mặt" đã được tạo sẵn cho bạn.');
        redirect('tong-quan');
    }

    public function logout(): void
    {
        Auth::logout();
        flash('success', 'Bạn đã đăng xuất.');
        redirect('');
    }
}
