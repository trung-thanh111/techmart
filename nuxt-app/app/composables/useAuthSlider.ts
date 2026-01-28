interface AuthItem {
  title: string;
}

type SplideOptions = any;

export function useAuthSlider() {
  const authOptions: SplideOptions = {
    type: "loop",
    perPage: 1,
    perMove: 1,
    autoplay: true,
    interval: 2500,
    arrows: false,
    pagination: false,
  };

  const loginItems: AuthItem[] = [
    {
      title: " Đăng nhập nhanh chóng, bảo mật tuyệt đối – an tâm trải nghiệm mọi dịch vụ! ",
    },
    {
      title: " Tài khoản của bạn được đảm bảo luôn luôn trạng thái an toàn. ",
    },
    {
      title: " Chỉ cần một lần đăng nhập, tận hưởng tiện ích mua sắm an toàn và cá nhân hóa! ",
    },
  ];

  const registerItems: AuthItem[] = [
    {
      title: " Đăng ký tài khoản mới để nhận ưu đãi hấp dẫn và trải nghiệm mua sắm tiện lợi! ",
    },
    {
      title: " Chỉ mất 1 phút để tạo tài khoản, bảo mật tuyệt đối và quản lý đơn hàng dễ dàng. ",
    },
    {
      title: " Đăng ký ngay để cá nhân hóa trải nghiệm và nhận thông báo khuyến mãi sớm nhất! ",
    },
  ];

  const forgotItems: AuthItem[] = [
    {
      title: " Quên mật khẩu? Đừng lo lắng, hãy làm theo các bước để khôi phục tài khoản an toàn! ",
    },
    {
      title: " Bảo mật là ưu tiên hàng đầu. Chúng tôi giúp bạn lấy lại mật khẩu chỉ trong vài phút. ",
    },
    {
      title: " Hãy đảm bảo mật khẩu mới đủ mạnh để bảo vệ thông tin cá nhân của bạn tốt nhất! ",
    },
  ];

  return {
    authOptions,
    loginItems,
    registerItems,
    forgotItems,
  };
}
