<html>
<head>
    <title>{{ $title  }}</title>
</head>
<body>
<div class="page-header">
    <h4>
        {{$title}}
    </h4>
</div>
<div>
    <p>
        Bạn hoặc ai đó đã gửi yêu cầu tạo mật khẩu mới cho tài khoản liên kết với địa chỉ email này trên hệ
        thống {{ $system_name or 'BTLWEB'  }}.
    </p>
    <p>
        Nếu đó là bạn và bạn đang quên mật khẩu, sử dụng mã xác thực sau để đăng nhập: <br>
        <a href="{{ $reset_link }}" target="_blank">
            <div style="text-align: center; border: 1px solid rgba(0, 0, 0, 0.2); padding: 10px 10px;">
                <code style="font-weight: bold; font-family: Consolas, monospace;">{{ $code }}</code>
            </div>
        </a>
    </p>


</div>

</body>
</html>