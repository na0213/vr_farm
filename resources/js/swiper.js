const swiper = new Swiper(".swiper", {
    loop: true,
    effect: "fade", // フェード切り替え
    // 自動再生
    autoplay: {
      delay: 4000, // 4秒後に次のスライドへ
      disableOnInteraction: false, // ユーザーが操作しても自動再生を継続
    },
    speed: 2000, // 2秒かけてフェード
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
  });

  const swiperCard = new Swiper(".swiper-card", {
    loop: true,
    speed: 1500,
    // 自動再生
    autoplay: {
      delay: 4000, // 4秒後に次のスライドへ
      disableOnInteraction: false, // ユーザーが操作しても自動再生を継続
    },
    pagination: {
      el: ".swiper-card .swiper-pagination", // ページネーションの指定
      clickable: true,
    },
  });
  // const swiperCard = new Swiper(".swiper-card", {
  //   loop: true,
  //   speed: 1000, // 少しゆっくり(デフォルトは300)
  //   autoplay: { // 自動再生
  //     delay: 1500, // 1.5秒後に次のスライド
  //     disableOnInteraction: false, // 矢印をクリックしても自動再生を止めない
  //   },
  //   // ページネーション
  //   pagination: {
  //     el: ".swiper-card .swiper-pagination",
  //     clickable: true,
  //   },
  //   // 前後の矢印
  //   navigation: {
  //     nextEl: ".swiper-card .swiper-button-next",
  //     prevEl: ".swiper-card .swiper-button-prev",
  //   },
  // });