interface LoginItem {
  title: string;
}

type SplideOptions = any;

export function useLoginSlider() {
  const loginOptions: SplideOptions = {
    type: "loop",
    perPage: 1,
    perMove: 1,
    autoplay: true,
    interval: 2500,
    arrows: false,
    pagination: false,
  };

  const loginItems: LoginItem[] = [
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

  return {
    loginOptions,
    loginItems,
  };
}
