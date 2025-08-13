<?php

namespace App\Http\Controllers;

use App\Mail\ResetPassword;
use App\Models\Address;
use App\Models\Password_reset_token;
use App\Models\User as ModelsUser;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule as ValidationRule;
use Laravel\Socialite\Facades\Socialite;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

Carbon::setLocale('vi');
date_default_timezone_set('Asia/Ho_Chi_Minh');
class User extends Controller
{
    private function generateUniqueUsername(int $length = 8, string $prefix = 'user_')
    {
        $username = '';
        $isUnique = false;

        do {
            $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
            $randomString = '';

            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }

            $generatedUsername = $prefix . $randomString;

            $isUnique = !ModelsUser::where('username', $generatedUsername)->exists();

            if ($isUnique) {
                $username = $generatedUsername;
            }
        } while (!$isUnique);

        return $username;
    }


    function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|unique:users|email:rfc,dns',
                'password' => 'required|confirmed|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
                'password_confirmation' => 'required|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
            ],
            [
                'email.required' => 'Vui lòng nhập email',
                'email.unique' => 'Email đã được sử dụng',
                'email.email' => 'Email không đúng định dạng',
                'password.required' => 'Vui lòng nhập mật khẩu',
                'password.confirmed' => 'Mật khẩu xác nhận không đúng',
                'password.min' => 'Mật khẩu phải có ít nhất 6 chữ số',
                'password.regex' => 'Mật khẩu phải có ít nhất 1 chữ hoa, 1 chữ thường, 1 số và 1 ký tự đặc biệt.',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        };

        $user = new ModelsUser;
        $user->username = $this->generateUniqueUsername();
        $user->password = Hash::make($request->password);
        $user->email = $request->email;
        $user->avatar = 'https://ui-avatars.com/api/?name=' . urlencode($user->username);
        $user->save();
        $token = $user->createToken('auth')->plainTextToken;
        return response()->json(
            [
                'message' => 'Đăng ký thành công',
                'user' => $user->id,
                'username' => $user->username,
                'token' => $token
            ],
            201
        );
    }

    function login(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email:rfc,dns',
                'password' => 'required|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
            ],
            [
                'email.required' => 'Vui lòng nhập email',
                'email.email' => 'Email không đúng định dạng',
                'password.required' => 'Vui lòng nhập mật khẩu',
                'password.min' => 'Mật khẩu phải có ít nhất 6 chữ số',
                'password.regex' => 'Mật khẩu phải có ít nhất 1 chữ hoa, 1 chữ thường, 1 số và 1 ký tự đặc biệt.',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        };

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Thông tin đăng nhập không chính xác.'
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth')->plainTextToken;
        return response()->json([
            'message' => 'Đăng nhập thành công!',
            'user' => $user->id,
            'username' => $user->username,
            'token' => $token
        ]);
    }

    function logout()
    {
        $user = Auth::user();
        $token = $user->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        return response()->json([
            'message' => 'Đăng xuất thành công!'
        ]);
    }

    function redirect($provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }

    function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
            $user = ModelsUser::where('email', $socialUser->getEmail())->first();

            // Email đã tồn tại và là tài khoản đăng ký bằng mật khẩu
            if ($user && $user->provider_name === null) {
                $token = $user->createToken('token')->plainTextToken;
                return redirect("http://localhost:5173/login-success?token=$token&login_existing_account=true");
            }

            // Email chưa tồn tại, tạo tài khoản mới
            if (!$user) {
                $user = ModelsUser::create([
                    'email' => $socialUser->getEmail(),
                    'username' => $this->generateUniqueUsername(),
                    'provider_id' => $socialUser->getId(),
                    'provider_name' => $provider,
                    'avatar' => $socialUser->getAvatar(),
                    'password' => bcrypt('default_password')
                ]);
            }

            // Đăng nhập thành công (tài khoản mới hoặc tài khoản social đã tồn tại)
            $token = $user->createToken('token')->plainTextToken;
            return redirect("http://localhost:5173/login-success?token=$token&username=" . urlencode($user->username) . '&id=' . $user->id);
            // return response()->json([
            //     'mess' => 'thành công'
            // ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Đăng nhập thất bại!',
                'message' => $e->getMessage()
            ], 401);
        }
    }

    function sendCode(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email:rfc,dns',
            ],
            [
                'email.required' => 'Vui lòng nhập email!',
                'email.email' => 'Email không đúng định dạng',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = ModelsUser::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Email không tồn tại trong hệ thống!'], 404);
        }

        $otpcode = random_int(10000, 99999);
        $password_reset = Password_reset_token::updateOrCreate(
            ['email' => $request->email],
            [
                'token' => $otpcode,
                'expires_at' => Carbon::now()->addMinute(5)
            ]
        );

        Mail::to($request->email)->send(new ResetPassword($otpcode));

        return response()->json([
            'mess' => 'Đã gửi mã otp!',
            'expires_at' => $password_reset->expires_at
        ]);
    }

    function verifyResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required'
        ], [
            'email.required' => 'Vui lòng nhập email',
            'token.required' => 'Vui lòng nhập mã khôi phục'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $user = Password_reset_token::where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'Mã xác minh không đúng'], 404);
        }

        if (now()->greaterThan($user->expires_at)) {
            return response()->json(['message' => 'Mã xác minh đã hết hạn'], 410);
        }

        $user->delete();


        return response()->json(['message' => 'Xác minh thành công']);
    }

    function resetPassword(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/|confirmed',
            ],
            [
                'email.required' => 'Vui lòng nhập email',
                'email.email' => 'Email không đúng định dạng',
                'password.required' => 'Vui lòng nhập mật khẩu',
                'password.min' => 'Mật khẩu phải có ít nhất 6 chữ số',
                'password.regex' => 'Mật khẩu phải có ít nhất 1 chữ hoa, 1 chữ thường, 1 số và 1 ký tự đặc biệt.',
                'password.confirmed' => 'Xác nhận mật khẩu không trùng khớp.',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        };

        $user = ModelsUser::where('email', $request->email)->first();
        $user->password = bcrypt($request->password);
        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(
            [
                'message' => 'Đặt lại mật khẩu thành công!',
                'username' => json_encode($user->username),
                'user' => $user->id,
                'token' => $token
            ]
        );
    }

    function getUserById()
    {
        $id = Auth::id();
        if (!$id) {
            return response()->json(['message' => 'User chưa đăng nhập'], 401);
        }

        $user = ModelsUser::with('addresses')->find($id);
        $defaultAddress = null;
        if ($user && $user->addresses) {
            $defaultAddress = $user->addresses->first(function ($address) {
                return $address->default == true;
            });
        }
        return response()->json([
            'user' => $user,
            'defaultAddress' => $defaultAddress
        ], 200);
    }
    function getAddressById(string $address_id)
    {
        $id = Auth::id();
        if (!$id) {
            return response()->json(['message' => 'User chưa đăng nhập'], 401);
        }
        $address = Address::find($address_id);
        Log::info($address);
        return response()->json([
            'address' => $address
        ], 200);
    }

    function insertAddress(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'customer_name' => 'required|string',
                'phone' => 'required|digits:10|unique:addresses,phone',
                'address' => 'required',
                'province_id' => 'required',
                'district_id' => 'required',
                'ward_code' => 'required',
            ],
            [
                'customer_name.required' => 'Vui lòng nhập tên',
                'customer_name.string' => 'Tên không đúng định dạng',
                'phone.required' => 'Vui lòng nhập số điện thoại',
                'phone.digits' => 'Số điện thoại phải đủ 10 chữ số',
                'phone.unique' => 'Số điện thoại đã tồn tại',
                'address.required' => 'Vui lòng nhập địa chỉ',
                'province_id.required' => 'Vui lòng nhập tỉnh/thành phố',
                'district_id.required' => 'Vui lòng nhập quận/huyện',
                'ward_code.required' => 'Vui lòng nhập phường/xã',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        $id = Auth::id();

        $id = Auth::id();
        if (!$id) {
            return response()->json(['message' => 'User chưa đăng nhập'], 401);
        }

        $address = new Address();
        $address->customer_id = $id;
        $address->customer_name = $request->customer_name;
        $address->phone = $request->phone;
        $address->address = $request->address;
        $address->province_id = $request->province_id;
        $address->district_id = $request->district_id;
        $address->ward_code = $request->ward_code;
        if ($request->default) {
            Address::where('customer_id', $id)
                ->update(['default' => false]);
        }
        $address->default = $request->default;


        if ($address->save()) {
            return response()->json([
                'mess' => 'Thêm thành công'
            ], 201);
        };
    }

    function editAddress(Request $request, string $address_id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'customer_name' => 'required|string',
                // Sửa rule digits thành 10 hoặc sử dụng regex để chặt chẽ hơn
                'phone' => ['required', 'regex:/^0[0-9]{9}$/'],
                ValidationRule::unique('addresses', 'phone')->ignore($address_id),
                'address' => 'required',
            ],
            [
                'customer_name.required' => 'Vui lòng nhập tên',
                'customer_name.string' => 'Tên không đúng định dạng',
                'phone.required' => 'Vui lòng nhập số điện thoại',
                'phone.regex' => 'Số điện thoại phải đủ 10 chữ số và bắt đầu bằng số 0.',
                'phone.unique' => 'Số điện thoại đã tồn tại',
                'address.required' => 'Vui lòng nhập địa chỉ',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        $id = Auth::id();

        $id = Auth::id();
        if (!$id) {
            return response()->json(['message' => 'User chưa đăng nhập'], 401);
        }

        $address = Address::find($address_id);
        if (!$address) {
            return response()->json([
                'mess' => 'Địa chỉ không tồn tại'
            ], 404);
        };

        $address->customer_id = $id;
        $address->customer_name = $request->customer_name;
        $address->phone = $request->phone;
        $address->address = $request->address;
        if ($request->default) {
            Address::where('customer_id', $id)
                ->update(['default' => false]);
        }
        $address->default = $request->default;

        if ($address->save()) {
            return response()->json([
                'mess' => 'Sửa thành công'
            ], 201);
        };
    }

    function updateAvatar(Request $request)
    {
        $id = Auth::id();
        if (!$id) {
            return response()->json(['message' => 'User chưa đăng nhập'], 401);
        }
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'avatar.required' => 'Vui lòng chọn một tập tin ảnh để tải lên.',
            'avatar.image' => 'Tập tin bạn chọn phải là một hình ảnh.',
            'avatar.mimes' => 'Định dạng hình ảnh không hợp lệ. Vui lòng chọn JPEG, PNG, JPG, GIF hoặc SVG.',
            'avatar.max' => 'Kích thước tập tin quá lớn. Vui lòng chọn một tập tin nhỏ hơn 2MB.',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        $user = ModelsUser::find($id);
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $tempPath = $file->getRealPath();
            $uploadedFileUrl = Cloudinary::upload($tempPath, [
                'folder' => 'avatars'
            ])->getSecurePath();

            $user->avatar = $uploadedFileUrl;
        }
        $user->save();
        return response()->json(['message' => 'Cập nhật avatar thành công', 'avatar' => $user->avatar]);
    }
}
