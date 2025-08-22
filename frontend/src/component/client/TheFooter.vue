<template>
  <footer class="bg-white pt-3">
    <div class="container">
      <div class="row">
        <div class="col-md-4 mb-4 text-start">
          <!-- <img src="../../public/img/logonew.png" alt="logo-footer" width="150px"> -->
          <p class="text-start">
            <img src="/images/logo/logo.svg" alt="Logo" class="logo" width="100px" />
          </p>
          <p>
            <strong style="color: blue;">Email:</strong> <a href="mailto:support@titokaqa.com"
              class="text-dark fw-bold">support@.com</a>
          </p>
          <p class="fw-bold">
            <strong style="color: blue;">Hotline:</strong> +84 888 888 888
          </p>
        </div>
        <div class="col-md-2 mb-4">
          <h5 class="fw-bold" style="color: blue;">Liên Kết Nhanh</h5>
          <ul class="list-unstyled">
            <li><a href="#" class="text-dark">Câu Hỏi Thường Gặp</a></li>
            <li><a href="#" class="text-dark">Điều Khoản & Điều Kiện</a></li>
            <li><a href="#" class="text-dark">Chính Sách Bảo Mật</a></li>
            <li><a href="#" class="text-dark">Liên Hệ</a></li>
          </ul>
        </div>
        <div class="col-md-3 mb-4">
          <h5 class="fw-bold" style="color: blue;">Theo Dõi Chúng Tôi</h5>
          <div class="d-flex gap-3 justify-content-center">
            <a href="#" class="text-dark"><i class="bi bi-facebook"></i></a>
            <a href="#" class="text-dark"><i class="bi bi-instagram"></i></a>
            <a href="#" class="text-dark"><i class="bi bi-tiktok"></i></a>
            <a href="#" class="text-dark"><i class="bi bi-youtube"></i></a>
          </div>
        </div>
        <div class="col-md-3 mb-4">
          <h5 class="fw-bold" style="color: blue;">Đăng Ký Nhận Tin</h5>
          <p>Nhận thông tin khuyến mãi .</p>
          <div class="input-group">
            <input type="email" class="form-control" placeholder="Nhập email của bạn">
            <button style="background-color: blue" class="btn btn-danger">Đăng Ký</button>
          </div>
        </div>
      </div>
      <hr class="text-secondary">
      <div class="row align-items-center">
        <div class="col-md-6 text-center text-md-start">
          <p class="mb-0">&copy; 2025 All Rights.</p>
        </div>
        <div class="col-md-6 text-center text-md-end">
          <!-- <img src="../../public/img/visa.png" alt="Visa" class="me-2" style="height: 30px;">
                        <img src="../../public/img/mastercard.png" alt="MasterCard" class="me-2" style="height: 30px;">
                        <img src="../../public/img/momo.png" alt="Momo" style="height: 30px;"> -->
        </div>
      </div>
    </div>
  </footer>
</template>
<style scoped>
/*footer*/
footer .row a {
  text-decoration: none;
  color: black;
}

/* Footer container spacing */
footer {
  margin-top: 40px;
  margin-bottom: 20px;
  background-color: #fff;
}

/* Logo size */
.logo-footer img {
  width: 150px;
}

@media (max-width: 768px) {
  .logo-footer img {
    width: 120px;
  }

  footer .row>div {
    text-align: center !important;
  }

  footer .row>div h5 {
    text-align: center;
  }

  footer .row>div p,
  footer .row>div ul {
    text-align: center;
    margin: 0 auto;
  }

  footer .d-flex.justify-content-center {
    justify-content: center !important;
  }

  footer .input-group {
    display: flex;
    gap: 10px;
  }

  footer .input-group input {
    width: 70% !important;
  }

  footer .input-group button {
    width: 25%;
    font-size: 16px;
  }

  footer .text-lg-start,
  footer .text-lg-end {
    text-align: center !important;
  }
}
</style>