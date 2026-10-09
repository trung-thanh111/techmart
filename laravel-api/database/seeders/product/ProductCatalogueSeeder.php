<?php

namespace Database\Seeders\Product;

use Illuminate\Database\Seeder;
use App\Models\ProductCatalogue;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductCatalogueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parents = [
            [
                "name" => "Điện thoại",
                "description" => "Điện thoại thông minh các loại",
                "order" => 1,
                "publish" => 2,
                "isHot" => true,
                "metaTitle" => "Điện thoại - Mua điện thoại di động giá rẻ",
                "metaKeyword" => "điện thoại, smartphone, di động",
                "metaDescription" => "Mua điện thoại thông minh chính hãng, giá tốt nhất. Đa dạng mẫu mã từ iPhone, Samsung, Xiaomi, OPPO và nhiều hãng khác."
            ],
            [
                "name" => "Tablet",
                "description" => "Máy tính bảng các loại",
                "order" => 2,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Tablet - Máy tính bảng chính hãng",
                "meta_keyword" => "tablet, máy tính bảng, ipad",
                "metaDescription" => "Máy tính bảng chính hãng từ Apple iPad, Samsung Galaxy Tab, Xiaomi Pad với giá tốt nhất."
            ],
            [
                "name" => "Laptop",
                "description" => "Laptop cho công việc, học tập và giải trí",
                "order" => 3,
                "publish" => 2,
                "isHot" => true,
                "metaTitle" => "Laptop - Máy tính xách tay giá rẻ, chính hãng",
                "metaKeyword" => "laptop, máy tính xách tay, laptop gaming, laptop văn phòng",
                "metaDescription" => "Mua laptop chính hãng, giá tốt. Đa dạng laptop văn phòng, gaming, đồ họa từ các thương hiệu uy tín."
            ],
            [
                "name" => "Âm thanh",
                "description" => "Thiết bị âm thanh chuyên nghiệp",
                "order" => 4,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Âm thanh - Thiết bị âm thanh chuyên nghiệp",
                "metaKeyword" => "tai nghe, loa, âm thanh, audio",
                "metaDescription" => "Thiết bị âm thanh chất lượng cao: tai nghe, loa cho audiophile và người yêu nhạc."
            ],
            [
                "name" => "Mic thu âm",
                "description" => "Micro thu âm chuyên nghiệp",
                "order" => 5,
                "publish" => 2,
                "isHot" => true,
                "metaTitle" => "Mic thu âm - Microphone chuyên nghiệp",
                "metaKeyword" => "mic thu âm, microphone, mic livestream",
                "metaDescription" => "Micro thu âm chuyên nghiệp cho streamer, podcaster, youtuber với chất lượng âm thanh tốt nhất."
            ],
            [
                "name" => "Đồng hồ",
                "description" => "Đồng hồ thông minh và định vị",
                "order" => 6,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Đồng hồ - Smartwatch chính hãng",
                "metaKeyword" => "đồng hồ thông minh, smartwatch, apple watch",
                "metaDescription" => "Đồng hồ thông minh chính hãng, theo dõi sức khỏe, công nghệ hiện đại, giá tốt."
            ],
            [
                "name" => "Camera",
                "description" => "Camera giám sát an ninh",
                "order" => 7,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Camera - Camera giám sát an ninh",
                "metaKeyword" => "camera, camera an ninh, camera giám sát",
                "metaDescription" => "Camera giám sát chất lượng cao, xem từ xa qua điện thoại, bảo vệ ngôi nhà an toàn."
            ],
            [
                "name" => "Đồ gia dụng",
                "description" => "Đồ gia dụng thông minh",
                "order" => 8,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Đồ gia dụng - Thiết bị gia đình thông minh",
                "metaKeyword" => "đồ gia dụng, thiết bị gia đình, smart home",
                "metaDescription" => "Đồ gia dụng thông minh tiện lợi cho gia đình hiện đại."
            ],
            [
                "name" => "Làm đẹp",
                "description" => "Thiết bị làm đẹp chăm sóc cá nhân",
                "order" => 9,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Làm đẹp - Thiết bị chăm sóc sắc đẹp",
                "metaKeyword" => "làm đẹp, thiết bị làm đẹp, chăm sóc cá nhân",
                "metaDescription" => "Thiết bị làm đẹp và chăm sóc cá nhân chất lượng cao cho phái đẹp."
            ],
            [
                "name" => "Phụ kiện",
                "description" => "Phụ kiện điện thoại, laptop và thiết bị công nghệ",
                "order" => 10,
                "publish" => 2,
                "isHot" => true,
                "metaTitle" => "Phụ kiện - Phụ kiện điện thoại, laptop chính hãng",
                "metaKeyword" => "phụ kiện, phụ kiện điện thoại, phụ kiện laptop",
                "metaDescription" => "Phụ kiện chính hãng cho điện thoại, laptop: ốp lưng, sạc dự phòng, cáp sạc, balo, chuột..."
            ],
            [
                "name" => "PC",
                "description" => "Máy tính để bàn và linh kiện",
                "order" => 11,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "PC - Máy tính để bàn chính hãng",
                "metaKeyword" => "pc, máy tính bàn, build pc, gaming pc",
                "metaDescription" => "Máy tính để bàn, linh kiện PC chất lượng cao cho văn phòng, gaming, đồ họa chuyên nghiệp."
            ],
            [
                "name" => "Màn hình",
                "description" => "Màn hình máy tính chuyên dụng",
                "order" => 12,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Màn hình - Màn hình máy tính cao cấp",
                "metaKeyword" => "màn hình, monitor, màn hình gaming",
                "metaDescription" => "Màn hình máy tính chất lượng cao cho văn phòng, gaming, thiết kế đồ họa."
            ],
            [
                "name" => "Máy in",
                "description" => "Máy in và thiết bị văn phòng",
                "order" => 13,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Máy in - Máy in văn phòng chính hãng",
                "metaKeyword" => "máy in, printer, máy in laser, máy in phun",
                "metaDescription" => "Máy in chất lượng cao cho văn phòng, gia đình với giá tốt nhất."
            ],
            [
                "name" => "Tivi",
                "description" => "Tivi thông minh các loại",
                "order" => 14,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Tivi - Smart TV chính hãng giá tốt",
                "metaKeyword" => "tivi, smart tv, tivi thông minh",
                "metaDescription" => "Tivi thông minh chính hãng, màn hình lớn, chất lượng 4K, giá tốt, bảo hành uy tín."
            ],
            [
                "name" => "Điện máy",
                "description" => "Thiết bị điện gia dụng",
                "order" => 15,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Điện máy - Thiết bị điện gia dụng",
                "metaKeyword" => "điện máy, thiết bị điện, đồ điện gia dụng",
                "metaDescription" => "Thiết bị điện máy gia đình chính hãng, giá tốt, bảo hành chu đáo."
            ]
        ];

        $children = [
            [
                "parentId" => 4,
                "name" => "Bluetooth",
                "description" => "Tai nghe Bluetooth không dây",
                "order" => 1,
                "publish" => 2,
                "isHot" => true,
                "metaTitle" => "Tai nghe Bluetooth - Tai nghe không dây chất lượng cao",
                "metaKeyword" => "tai nghe bluetooth, tai nghe không dây, wireless earbuds",
                "metaDescription" => "Tai nghe Bluetooth không dây chất lượng cao, âm thanh sống động, kết nối ổn định."
            ],
            [
                "parentId" => 4,
                "name" => "Chụp tai",
                "description" => "Tai nghe chụp tai over-ear",
                "order" => 2,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Tai nghe chụp tai - Over-ear headphones",
                "metaKeyword" => "tai nghe chụp tai, over ear, headphones",
                "metaDescription" => "Tai nghe chụp tai cao cấp, âm thanh vòm, cách âm tốt, phù hợp nghe nhạc và gaming."
            ],
            [
                "parentId" => 4,
                "name" => "Nhét tai",
                "description" => "Tai nghe nhét tai in-ear",
                "order" => 3,
                "publish" => 2,
                "isHot" => true,
                "metaTitle" => "Tai nghe nhét tai - In-ear earphones",
                "metaKeyword" => "tai nghe nhét tai, in ear, earphones",
                "metaDescription" => "Tai nghe nhét tai nhỏ gọn, tiện lợi, âm thanh chất lượng, phù hợp mọi hoạt động."
            ],
            [
                "parentId" => 4,
                "name" => "Có dây",
                "description" => "Tai nghe có dây",
                "order" => 4,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Tai nghe có dây - Wired headphones",
                "metaKeyword" => "tai nghe có dây, wired, tai nghe jack",
                "metaDescription" => "Tai nghe có dây chất lượng âm thanh tốt, không lo hết pin, giá cả phải chăng."
            ],
            [
                "parentId" => 4,
                "name" => "Thể thao",
                "description" => "Tai nghe thể thao chống nước",
                "order" => 5,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Tai nghe thể thao - Sports earphones",
                "metaKeyword" => "tai nghe thể thao, sports, chống nước, workout",
                "metaDescription" => "Tai nghe thể thao chống nước, chống mồ hôi, ôm sát tai, phù hợp tập luyện và chạy bộ."
            ],
            [
                "parentId" => 4,
                "name" => "Tai nghe Gaming",
                "description" => "Tai nghe gaming chuyên nghiệp",
                "order" => 6,
                "publish" => 2,
                "isHot" => true,
                "metaTitle" => "Tai nghe Gaming - Headset chơi game chuyên nghiệp",
                "metaKeyword" => "tai nghe gaming, gaming headset, tai nghe chơi game",
                "metaDescription" => "Tai nghe gaming chuyên nghiệp với âm thanh vòm 7.1, micro chống ồn, LED RGB đẹp mắt."
            ],
            [
                "parentId" => 4,
                "name" => "Xem tất cả tai nghe",
                "description" => "Tất cả các loại tai nghe",
                "order" => 7,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Tất cả tai nghe - Xem toàn bộ sản phẩm tai nghe",
                "metaKeyword" => "tai nghe, headphones, earphones, audio",
                "metaDescription" => "Xem tất cả các loại tai nghe: Bluetooth, có dây, chụp tai, nhét tai, gaming, thể thao."
            ],
            [
                "parentId" => 5,
                "name" => "Mic cài áo",
                "description" => "Micro cài áo thu âm",
                "order" => 1,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Mic cài áo - Lavalier microphone",
                "metaKeyword" => "mic cài áo, lavalier, micro thu âm",
                "metaDescription" => "Mic cài áo chất lượng cao cho quay video, livestream, phỏng vấn, thu âm di động."
            ],
            [
                "parentId" => 5,
                "name" => "Mic phòng thu",
                "description" => "Micro phòng thu chuyên nghiệp",
                "order" => 2,
                "publish" => 2,
                "isHot" => true,
                "metaTitle" => "Mic phòng thu, podcast - Studio microphone",
                "metaKeyword" => "mic phòng thu, studio mic, podcast microphone",
                "metaDescription" => "Micro phòng thu chuyên nghiệp cho podcast, livestream, thu âm chất lượng studio."
            ],
            [
                "parentId" => 5,
                "name" => "Mic livestream",
                "description" => "Micro livestream chuyên dụng",
                "order" => 3,
                "publish" => 2,
                "isHot" => true,
                "metaTitle" => "Mic livestream - Microphone cho streamer",
                "metaKeyword" => "mic livestream, streaming mic, micro streamer",
                "metaDescription" => "Micro livestream chất lượng cao, thu âm rõ ràng, phù hợp cho streamer, youtuber."
            ],
            [
                "parentId" => 5,
                "name" => "Micro không dây",
                "description" => "Micro không dây thu âm",
                "order" => 4,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Micro không dây - Wireless microphone",
                "metaKeyword" => "micro không dây, wireless mic, mic karaoke",
                "metaDescription" => "Micro không dây tiện lợi, kết nối ổn định, phù hợp hát karaoke, thuyết trình, dạy học."
            ],
            [
                "parentId" => 6,
                "name" => "Đồng hồ thông minh",
                "description" => "Smartwatch đa chức năng",
                "order" => 1,
                "publish" => 2,
                "isHot" => true,
                "metaTitle" => "Đồng hồ thông minh - Smartwatch chính hãng",
                "metaKeyword" => "đồng hồ thông minh, smartwatch, đồng hồ thông minh",
                "metaDescription" => "Đồng hồ thông minh chính hãng, theo dõi sức khỏe, thông báo cuộc gọi, nhiều tính năng."
            ],
            [
                "parentId" => 6,
                "name" => "Vòng đeo tay thông minh",
                "description" => "Vòng đeo tay theo dõi sức khỏe",
                "order" => 2,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Vòng đeo tay thông minh - Smart band",
                "metaKeyword" => "vòng đeo tay, smart band, fitness tracker",
                "metaDescription" => "Vòng đeo tay thông minh theo dõi sức khỏe, bước chân, nhịp tim, giấc ngủ chính xác."
            ],
            [
                "parentId" => 6,
                "name" => "Đồng hồ định vị trẻ em",
                "description" => "Đồng hồ định vị GPS cho trẻ em",
                "order" => 3,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Đồng hồ định vị trẻ em - Kids GPS watch",
                "metaKeyword" => "đồng hồ trẻ em, gps watch, định vị trẻ em",
                "metaDescription" => "Đồng hồ định vị GPS cho trẻ em an toàn, gọi điện, nhắn tin, theo dõi vị trí chính xác."
            ],
            [
                "parentId" => 6,
                "name" => "Dây đeo",
                "description" => "Dây đeo thay thế cho smartwatch",
                "order" => 4,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Dây đeo đồng hồ - Watch bands",
                "metaKeyword" => "dây đeo, watch band, dây đồng hồ",
                "metaDescription" => "Dây đeo thay thế cho đồng hồ thông minh, đa dạng màu sắc, chất liệu cao cấp."
            ],
            [
                "parentId" => 10,
                "name" => "Phụ kiện Apple",
                "description" => "Phụ kiện chính hãng cho thiết bị Apple",
                "order" => 1,
                "publish" => 2,
                "isHot" => true,
                "metaTitle" => "Phụ kiện Apple - Apple accessories chính hãng",
                "metaKeyword" => "phụ kiện apple, apple accessories, airpods, apple watch",
                "metaDescription" => "Phụ kiện chính hãng Apple: AirPods, Apple Watch, sạc, cáp, ốp lưng cho iPhone, iPad."
            ],
            [
                "parentId" => 10,
                "name" => "Dán màn hình",
                "description" => "Miếng dán bảo vệ màn hình",
                "order" => 2,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Dán màn hình - Screen protector chất lượng cao",
                "metaKeyword" => "dán màn hình, screen protector, kính cường lực",
                "metaDescription" => "Miếng dán màn hình, kính cường lực bảo vệ màn hình điện thoại, tablet hiệu quả."
            ],
            [
                "parentId" => 10,
                "name" => "Ốp lưng",
                "description" => "Ốp lưng bảo vệ điện thoại",
                "order" => 3,
                "publish" => 2,
                "isHot" => true,
                "metaTitle" => "Ốp lưng - Case bảo vệ điện thoại",
                "metaKeyword" => "ốp lưng, case, ốp điện thoại",
                "metaDescription" => "Ốp lưng đẹp, bảo vệ điện thoại chống sốc, chống trầy, đa dạng kiểu dáng."
            ],
            [
                "parentId" => 10,
                "name" => "Thẻ nhớ",
                "description" => "Thẻ nhớ mở rộng dung lượng",
                "order" => 4,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Thẻ nhớ - Memory card tốc độ cao",
                "metaKeyword" => "thẻ nhớ, memory card, sd card",
                "metaDescription" => "Thẻ nhớ dung lượng lớn, tốc độ cao cho điện thoại, máy ảnh, camera hành trình."
            ],
            [
                "parentId" => 10,
                "name" => "Sim 4G - 5G",
                "description" => "Sim 4G 5G các nhà mạng",
                "order" => 5,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Sim 4G - 5G - Sim số đẹp các nhà mạng",
                "metaKeyword" => "sim 4g, sim 5g, sim số đẹp",
                "metaDescription" => "Sim 4G, 5G các nhà mạng, sim số đẹp, gói cước ưu đãi, tốc độ cao."
            ],
            [
                "parentId" => 10,
                "name" => "Cáp, Sạc",
                "description" => "Cáp sạc và củ sạc",
                "order" => 6,
                "publish" => 2,
                "isHot" => true,
                "metaTitle" => "Cáp, Sạc - Cáp sạc và củ sạc chính hãng",
                "metaKeyword" => "cáp sạc, củ sạc, adapter, charging cable",
                "metaDescription" => "Cáp sạc và củ sạc chính hãng, sạc nhanh, bền bỉ cho mọi thiết bị."
            ],
            [
                "parentId" => 10,
                "name" => "Pin dự phòng",
                "description" => "Pin sạc dự phòng di động",
                "order" => 7,
                "publish" => 2,
                "isHot" => true,
                "metaTitle" => "Pin dự phòng - Power bank dung lượng cao",
                "metaKeyword" => "pin dự phòng, power bank, sạc dự phòng",
                "metaDescription" => "Pin sạc dự phòng dung lượng lớn, sạc nhanh, nhỏ gọn tiện mang theo."
            ],
            [
                "parentId" => 10,
                "name" => "Dây đeo chéo điện thoại",
                "description" => "Dây đeo điện thoại tiện lợi",
                "order" => 8,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Dây đeo chéo điện thoại - Phone strap",
                "metaKeyword" => "dây đeo điện thoại, phone strap, dây đeo chéo",
                "metaDescription" => "Dây đeo điện thoại thời trang, tiện lợi, giải phóng tay khi di chuyển."
            ],
            [
                "parentId" => 10,
                "name" => "Phụ kiện điện thoại",
                "description" => "Các loại phụ kiện điện thoại khác",
                "order" => 9,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Phụ kiện điện thoại - Phone accessories",
                "metaKeyword" => "phụ kiện điện thoại, phone accessories",
                "metaDescription" => "Phụ kiện điện thoại đa dạng: giá đỡ, gậy chụp ảnh, quạt mini và nhiều phụ kiện khác."
            ],
            [
                "parentId" => 3,
                "name" => "Chuột",
                "description" => "Chuột máy tính các loại",
                "order" => 1,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Chuột - Chuột máy tính gaming, văn phòng",
                "metaKeyword" => "chuột, mouse, chuột gaming, chuột không dây",
                "metaDescription" => "Chuột máy tính cho gaming, văn phòng, có dây và không dây với độ chính xác cao."
            ],
            [
                "parentId" => 3,
                "name" => "Bàn phím",
                "description" => "Bàn phím máy tính cơ và màng",
                "order" => 2,
                "publish" => 2,
                "isHot" => true,
                "metaTitle" => "Bàn phím - Keyboard cơ, gaming",
                "metaKeyword" => "bàn phím, keyboard, bàn phím cơ, mechanical keyboard",
                "metaDescription" => "Bàn phím cơ, gaming keyboard, bàn phím văn phòng chất lượng cao, gõ êm ái."
            ],
            [
                "parentId" => 3,
                "name" => "Balo Laptop",
                "description" => "Balo đựng laptop chống sốc",
                "order" => 3,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Balo Laptop - Túi chống sốc",
                "metaKeyword" => "balo laptop, túi laptop, túi chống sốc",
                "metaDescription" => "Balo đựng laptop chống sốc, nhiều ngăn tiện lợi, phong cách hiện đại."
            ],
            [
                "parentId" => 3,
                "name" => "Phần mềm",
                "description" => "Phần mềm bản quyền",
                "order" => 4,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Phần mềm - Software bản quyền",
                "metaKeyword" => "phần mềm, software, windows, office",
                "metaDescription" => "Phần mềm bản quyền: Windows, Office, antivirus và các phần mềm chuyên dụng."
            ],
            [
                "parentId" => 3,
                "name" => "Webcam",
                "description" => "Webcam cho học tập và làm việc",
                "order" => 5,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Webcam - Camera học online, họp online",
                "metaKeyword" => "webcam, camera online, webcam học online",
                "metaDescription" => "Webcam chất lượng cao cho học online, họp online, livestream với hình ảnh sắc nét."
            ],
            [
                "parentId" => 3,
                "name" => "Giá đỡ",
                "description" => "Giá đỡ laptop, tablet",
                "order" => 6,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Giá đỡ - Stand cho laptop, tablet",
                "metaKeyword" => "giá đỡ laptop, laptop stand, giá đỡ tablet",
                "metaDescription" => "Giá đỡ laptop, tablet tản nhiệt tốt, điều chỉnh góc linh hoạt, bảo vệ cột sống."
            ],
            [
                "parentId" => 3,
                "name" => "Thảm, lót chuột",
                "description" => "Thảm và lót chuột gaming",
                "order" => 7,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Thảm, lót chuột - Gaming mousepad",
                "metaKeyword" => "lót chuột, mousepad, thảm chuột gaming",
                "metaDescription" => "Lót chuột gaming size lớn, bề mặt mượt mà, đế chống trượt, bền đẹp."
            ],
            [
                "parentId" => 3,
                "name" => "Sạc laptop",
                "description" => "Adapter sạc laptop chính hãng",
                "order" => 8,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Sạc laptop - Adapter sạc chính hãng",
                "metaKeyword" => "sạc laptop, adapter, củ sạc laptop",
                "metaDescription" => "Adapter sạc laptop chính hãng, tương thích nhiều dòng máy, an toàn hiệu quả."
            ],
            [
                "parentId" => 3,
                "name" => "Camera phòng họp",
                "description" => "Camera hội nghị chuyên nghiệp",
                "order" => 9,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Camera phòng họp - Conference camera",
                "metaKeyword" => "camera phòng họp, conference camera, camera hội nghị",
                "metaDescription" => "Camera phòng họp chất lượng cao, góc rộng, micro tích hợp cho cuộc họp chuyên nghiệp."
            ],
            [
                "parentId" => 3,
                "name" => "Hub chuyển đổi",
                "description" => "Hub USB chuyển đổi cổng",
                "order" => 10,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Hub chuyển đổi - USB Hub, Type-C Hub",
                "metaKeyword" => "hub, usb hub, type c hub, đầu chuyển",
                "metaDescription" => "Hub chuyển đổi USB, Type-C mở rộng cổng kết nối cho laptop, máy tính."
            ],
            [
                "parentId" => 11,
                "name" => "Build PC",
                "description" => "Build PC theo yêu cầu",
                "order" => 1,
                "publish" => 2,
                "isHot" => true,
                "metaTitle" => "Build PC - Lắp ráp máy tính theo yêu cầu",
                "metaKeyword" => "build pc, lắp ráp pc, máy tính custom",
                "metaDescription" => "Dịch vụ build PC theo yêu cầu, tư vấn cấu hình phù hợp cho gaming, đồ họa, văn phòng."
            ],
            [
                "parentId" => 11,
                "name" => "Cấu hình sẵn",
                "description" => "PC build sẵn nguyên bộ",
                "order" => 2,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Cấu hình sẵn - PC build sẵn",
                "metaKeyword" => "pc build sẵn, máy tính bộ, desktop pc",
                "metaDescription" => "PC build sẵn cấu hình tối ưu, sẵn sàng sử dụng ngay, bảo hành chính hãng."
            ],
            [
                "parentId" => 11,
                "name" => "All In One",
                "description" => "Máy tính All In One",
                "order" => 3,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "All In One - Máy tính All In One",
                "metaKeyword" => "all in one, aio, máy tính all in one",
                "metaDescription" => "Máy tính All In One gọn gàng, tiết kiệm không gian, hiệu năng mạnh mẽ."
            ],
            [
                "parentId" => 11,
                "name" => "PC bộ",
                "description" => "PC để bàn văn phòng",
                "order" => 4,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "PC bộ - Máy tính để bàn văn phòng",
                "metaKeyword" => "pc bộ, máy tính bàn, desktop",
                "metaDescription" => "PC bộ cho văn phòng, học tập với cấu hình ổn định, giá tốt."
            ],
            [
                "parentId" => 11,
                "name" => "PC Gaming",
                "description" => "PC Gaming cấu hình cao",
                "order" => 5,
                "publish" => 2,
                "isHot" => true,
                "metaTitle" => "PC Gaming - Gaming PC cấu hình mạnh",
                "metaKeyword" => "pc gaming, gaming pc, máy tính chơi game",
                "metaDescription" => "PC Gaming cấu hình cao, chơi game mượt mà, tản nhiệt tốt, LED RGB đẹp mắt."
            ],
            [
                "parentId" => 11,
                "name" => "Đồ họa",
                "description" => "PC workstation cho đồ họa",
                "order" => 6,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "PC Đồ họa - Workstation chuyên nghiệp",
                "metaKeyword" => "pc đồ họa, workstation, máy trạm",
                "metaDescription" => "PC workstation cho đồ họa, render 3D, edit video chuyên nghiệp với hiệu năng cao."
            ],
            [
                "parentId" => 11,
                "name" => "Văn phòng",
                "description" => "PC văn phòng giá rẻ",
                "order" => 7,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "PC Văn phòng - Máy tính văn phòng giá rẻ",
                "metaKeyword" => "pc văn phòng, máy tính văn phòng, office pc",
                "metaDescription" => "PC văn phòng giá rẻ, cấu hình ổn định cho công việc hành chính, kế toán."
            ],
            [
                "parentId" => 12,
                "name" => "Case",
                "description" => "Vỏ case máy tính",
                "order" => 8,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Case - Vỏ case máy tính đẹp",
                "metaKeyword" => "case, vỏ case, thùng máy",
                "metaDescription" => "Vỏ case máy tính đẹp, tản nhiệt tốt, nhiều màu sắc, LED RGB."
            ],
            [
                "parentId" => 12,
                "name" => "Xem tất cả",
                "description" => "Tất cả linh kiện máy tính",
                "order" => 9,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Xem tất cả - Linh kiện máy tính",
                "metaKeyword" => "linh kiện pc, linh kiện máy tính",
                "metaDescription" => "Xem tất cả linh kiện máy tính: CPU, Main, RAM, VGA, SSD và nhiều linh kiện khác."
            ],
            [
                "parentId" => 10,
                "name" => "Chuột Gaming",
                "description" => "Chuột máy tính gaming, văn phòng",
                "order" => 10,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Chuột - Mouse gaming, văn phòng",
                "metaKeyword" => "chuột, mouse, chuột gaming",
                "metaDescription" => "Chuột máy tính cho gaming, văn phòng với độ chính xác cao."
            ],
            [
                "parentId" => 10,
                "name" => "Bàn phím Cơ",
                "description" => "Bàn phím cơ và gaming",
                "order" => 11,
                "publish" => 2,
                "isHot" => true,
                "metaTitle" => "Bàn phím - Keyboard cơ, gaming",
                "metaKeyword" => "bàn phím, keyboard, bàn phím cơ",
                "metaDescription" => "Bàn phím cơ, gaming keyboard chất lượng cao, gõ êm ái."
            ],
            [
                "parentId" => 10,
                "name" => "Balo Gaming",
                "description" => "Balo laptop chống sốc",
                "order" => 12,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Balo - Túi chống sốc laptop",
                "metaKeyword" => "balo laptop, túi laptop",
                "metaDescription" => "Balo đựng laptop chống sốc, nhiều ngăn tiện lợi."
            ],
            [
                "parentId" => 10,
                "name" => "Túi chống sốc",
                "description" => "Túi chống sốc bảo vệ laptop",
                "order" => 13,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Túi chống sốc - Sleeve laptop",
                "metaKeyword" => "túi chống sốc, sleeve, túi laptop",
                "metaDescription" => "Túi chống sốc bảo vệ laptop an toàn khi di chuyển."
            ],
            [
                "parentId" => 10,
                "name" => "Phần mềm Laptop",
                "description" => "Phần mềm bản quyền",
                "order" => 14,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Phần mềm - Software bản quyền",
                "metaKeyword" => "phần mềm, software, windows, office",
                "metaDescription" => "Phần mềm bản quyền: Windows, Office và các software chuyên dụng."
            ],
            [
                "parentId" => 10,
                "name" => "Webcam Laptop",
                "description" => "Webcam học online, họp online",
                "order" => 15,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Webcam - Camera học online",
                "metaKeyword" => "webcam, camera online",
                "metaDescription" => "Webcam chất lượng cao cho học online, họp online, livestream."
            ],
            [
                "parentId" => 10,
                "name" => "Giá đỡ Laptop",
                "description" => "Giá đỡ laptop, tablet",
                "order" => 16,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Giá đỡ - Stand laptop, tablet",
                "metaKeyword" => "giá đỡ laptop, laptop stand",
                "metaDescription" => "Giá đỡ laptop tản nhiệt tốt, điều chỉnh góc linh hoạt."
            ],
            [
                "parentId" => 10,
                "name" => "Thảm Gaming",
                "description" => "Thảm lót chuột gaming",
                "order" => 17,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Thảm - Mousepad gaming",
                "metaKeyword" => "thảm chuột, mousepad, lót chuột",
                "metaDescription" => "Thảm lót chuột gaming size lớn, bề mặt mượt mà."
            ],
            [
                "parentId" => 10,
                "name" => "Lót chuột",
                "description" => "Lót chuột văn phòng",
                "order" => 18,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Lót chuột - Mousepad văn phòng",
                "metaKeyword" => "lót chuột, mousepad",
                "metaDescription" => "Lót chuột văn phòng, đế chống trượt, bền đẹp."
            ],
            [
                "parentId" => 10,
                "name" => "Adapter Sạc",
                "description" => "Adapter sạc laptop",
                "order" => 19,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Sạc laptop - Adapter chính hãng",
                "metaKeyword" => "sạc laptop, adapter",
                "metaDescription" => "Adapter sạc laptop chính hãng, tương thích nhiều dòng máy."
            ],
            [
                "parentId" => 10,
                "name" => "Webcam Phòng họp",
                "description" => "Camera hội nghị chuyên nghiệp",
                "order" => 20,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Camera phòng họp - Conference camera",
                "metaKeyword" => "camera phòng họp, conference camera",
                "metaDescription" => "Camera phòng họp chất lượng cao cho cuộc họp chuyên nghiệp."
            ],
            [
                "parentId" => 10,
                "name" => "USB Hub",
                "description" => "Hub USB chuyển đổi",
                "order" => 21,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Hub chuyển đổi - USB Hub",
                "metaKeyword" => "hub, usb hub, type c hub",
                "metaDescription" => "Hub chuyển đổi mở rộng cổng kết nối cho laptop."
            ],
            [
                "parentId" => 10,
                "name" => "PlayStation",
                "description" => "Máy chơi game PlayStation",
                "order" => 22,
                "publish" => 2,
                "isHot" => true,
                "metaTitle" => "PlayStation - PS5, PS4 chính hãng",
                "metaKeyword" => "playstation, ps5, ps4",
                "metaDescription" => "PlayStation chính hãng: PS5, PS4 với game đa dạng."
            ],
            [
                "parentId" => 10,
                "name" => "ROG Ally",
                "description" => "Máy chơi game cầm tay ROG Ally",
                "order" => 23,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "ROG Ally - Gaming handheld",
                "metaKeyword" => "rog ally, gaming handheld",
                "metaDescription" => "ROG Ally máy chơi game cầm tay mạnh mẽ."
            ],
            [
                "parentId" => 10,
                "name" => "MSI Claw",
                "description" => "Máy chơi game cầm tay MSI Claw",
                "order" => 24,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "MSI Claw - Handheld gaming",
                "metaKeyword" => "msi claw, handheld gaming",
                "metaDescription" => "MSI Claw máy chơi game cầm tay hiệu năng cao."
            ],
            [
                "parentId" => 10,
                "name" => "Bàn phím Gaming",
                "description" => "Bàn phím gaming chuyên nghiệp",
                "order" => 25,
                "publish" => 2,
                "isHot" => true,
                "metaTitle" => "Bàn phím Gaming - Mechanical keyboard",
                "metaKeyword" => "bàn phím gaming, mechanical keyboard",
                "metaDescription" => "Bàn phím gaming chuyên nghiệp với switch cơ học chất lượng."
            ],
            [
                "parentId" => 10,
                "name" => "Chuột chơi game",
                "description" => "Chuột gaming chuyên dụng",
                "order" => 26,
                "publish" => 2,
                "isHot" => true,
                "metaTitle" => "Chuột chơi game - Gaming mouse",
                "metaKeyword" => "chuột gaming, gaming mouse",
                "metaDescription" => "Chuột chơi game với DPI cao, phản hồi nhanh."
            ],
            [
                "parentId" => 10,
                "name" => "Headset Gaming",
                "description" => "Tai nghe gaming âm thanh vòm",
                "order" => 27,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Tai nghe Gaming - Gaming headset",
                "metaKeyword" => "tai nghe gaming, gaming headset",
                "metaDescription" => "Tai nghe gaming âm thanh vòm 7.1, micro chống ồn."
            ],
            [
                "parentId" => 10,
                "name" => "Tay cầm chơi game",
                "description" => "Tay cầm chơi game gamepad",
                "order" => 28,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Tay cầm chơi game - Gamepad",
                "metaKeyword" => "tay cầm, gamepad, controller",
                "metaDescription" => "Tay cầm chơi game cho PC, PS, Xbox với độ chính xác cao."
            ],
            [
                "parentId" => 10,
                "name" => "Xem tất cả Gaming Gear",
                "description" => "Tất cả thiết bị gaming",
                "order" => 29,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Xem tất cả Gaming Gear",
                "metaKeyword" => "gaming gear, thiết bị gaming",
                "metaDescription" => "Xem tất cả thiết bị gaming: bàn phím, chuột, tai nghe, tay cầm."
            ],
            [
                "parentId" => 10,
                "name" => "Dây đeo đồng hồ",
                "description" => "Dây đeo thay thế cho đồng hồ",
                "order" => 30,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Dây đeo đồng hồ - Watch strap",
                "metaKeyword" => "dây đeo đồng hồ, watch strap",
                "metaDescription" => "Dây đeo thay thế cho smartwatch, đa dạng màu sắc."
            ],
            [
                "parentId" => 10,
                "name" => "Dây đeo Airtag",
                "description" => "Dây đeo cho Apple Airtag",
                "order" => 31,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Dây đeo Airtag - Airtag holder",
                "metaKeyword" => "dây đeo airtag, airtag holder",
                "metaDescription" => "Dây đeo gắn Airtag tiện lợi cho túi, chìa khóa."
            ],
            [
                "parentId" => 10,
                "name" => "Phụ kiện tiện ích",
                "description" => "Phụ kiện công nghệ tiện ích",
                "order" => 32,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Phụ kiện tiện ích - Tech accessories",
                "metaKeyword" => "phụ kiện tiện ích, tech accessories",
                "metaDescription" => "Phụ kiện công nghệ tiện ích cho cuộc sống hiện đại."
            ],
            [
                "parentId" => 10,
                "name" => "Phụ kiện ô tô",
                "description" => "Phụ kiện công nghệ cho xe hơi",
                "order" => 33,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Phụ kiện ô tô - Car accessories",
                "metaKeyword" => "phụ kiện ô tô, car accessories",
                "metaDescription" => "Phụ kiện công nghệ cho xe hơi: camera hành trình, sạc xe..."
            ],
            [
                "parentId" => 10,
                "name" => "Bút cảm ứng",
                "description" => "Bút cảm ứng cho tablet",
                "order" => 34,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Bút cảm ứng - Stylus pen",
                "metaKeyword" => "bút cảm ứng, stylus, apple pencil",
                "metaDescription" => "Bút cảm ứng cho tablet, vẽ và ghi chú chuyên nghiệp."
            ],
            [
                "parentId" => 10,
                "name" => "Thiết bị định vị",
                "description" => "Thiết bị định vị GPS tracker",
                "order" => 35,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Thiết bị định vị - GPS tracker",
                "metaKeyword" => "thiết bị định vị, gps tracker, airtag",
                "metaDescription" => "Thiết bị định vị GPS theo dõi vị trí chính xác."
            ],
            [
                "parentId" => 13,
                "name" => "Màn hình ASUS",
                "description" => "Màn hình ASUS chính hãng",
                "order" => 1,
                "publish" => 2,
                "isHot" => true,
                "metaTitle" => "Màn hình ASUS - ASUS monitor",
                "metaKeyword" => "màn hình asus, asus monitor",
                "metaDescription" => "Màn hình ASUS chất lượng cao cho gaming và văn phòng."
            ],
            [
                "parentId" => 13,
                "name" => "Màn hình Samsung",
                "description" => "Màn hình Samsung chính hãng",
                "order" => 2,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Màn hình Samsung - Samsung monitor",
                "metaKeyword" => "màn hình samsung, samsung monitor",
                "metaDescription" => "Màn hình Samsung màu sắc sống động, thiết kế đẹp."
            ],
            [
                "parentId" => 13,
                "name" => "Màn hình DELL",
                "description" => "Màn hình DELL chính hãng",
                "order" => 3,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Màn hình DELL - DELL monitor",
                "metaKeyword" => "màn hình dell, dell monitor",
                "metaDescription" => "Màn hình DELL chuyên nghiệp cho văn phòng và thiết kế."
            ],
            [
                "parentId" => 13,
                "name" => "Màn hình LG",
                "description" => "Màn hình LG chính hãng",
                "order" => 4,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Màn hình LG - LG monitor",
                "metaKeyword" => "màn hình lg, lg monitor",
                "metaDescription" => "Màn hình LG công nghệ IPS, màu sắc chuẩn xác."
            ],
            [
                "parentId" => 13,
                "name" => "Màn hình MSI",
                "description" => "Màn hình MSI gaming",
                "order" => 5,
                "publish" => 2,
                "isHot" => true,
                "metaTitle" => "Màn hình MSI - MSI gaming monitor",
                "metaKeyword" => "màn hình msi, msi monitor",
                "metaDescription" => "Màn hình MSI gaming tần số quét cao, phản hồi nhanh."
            ],
            [
                "parentId" => 13,
                "name" => "Màn hình Acer",
                "description" => "Màn hình Acer chính hãng",
                "order" => 6,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Màn hình Acer - Acer monitor",
                "metaKeyword" => "màn hình acer, acer monitor",
                "metaDescription" => "Màn hình Acer giá tốt, chất lượng ổn định."
            ],
            [
                "parentId" => 13,
                "name" => "Màn hình Xiaomi",
                "description" => "Màn hình Xiaomi giá rẻ",
                "order" => 7,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Màn hình Xiaomi - Xiaomi monitor",
                "metaKeyword" => "màn hình xiaomi, xiaomi monitor",
                "metaDescription" => "Màn hình Xiaomi giá rẻ, chất lượng tốt."
            ],
            [
                "parentId" => 13,
                "name" => "Màn hình ViewSonic",
                "description" => "Màn hình ViewSonic chuyên nghiệp",
                "order" => 8,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Màn hình ViewSonic - ViewSonic monitor",
                "metaKeyword" => "màn hình viewsonic, viewsonic monitor",
                "metaDescription" => "Màn hình ViewSonic chuyên nghiệp cho đồ họa."
            ],
            [
                "parentId" => 13,
                "name" => "Màn hình Philips",
                "description" => "Màn hình Philips bảo vệ mắt",
                "order" => 9,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Màn hình Philips - Philips monitor",
                "metaKeyword" => "màn hình philips, philips monitor",
                "metaDescription" => "Màn hình Philips công nghệ bảo vệ mắt."
            ],
            [
                "parentId" => 13,
                "name" => "Màn hình AOC",
                "description" => "Màn hình AOC gaming",
                "order" => 10,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Màn hình AOC - AOC monitor",
                "metaKeyword" => "màn hình aoc, aoc monitor",
                "metaDescription" => "Màn hình AOC gaming giá tốt, tần số cao."
            ],
            [
                "parentId" => 13,
                "name" => "Màn hình Dahua",
                "description" => "Màn hình Dahua giám sát",
                "order" => 11,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Màn hình Dahua - Dahua monitor",
                "metaKeyword" => "màn hình dahua, dahua monitor",
                "metaDescription" => "Màn hình Dahua chuyên dụng cho camera giám sát."
            ],
            [
                "parentId" => 13,
                "name" => "Màn hình KOORUI",
                "description" => "Màn hình KOORUI gaming",
                "order" => 12,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Màn hình KOORUI - KOORUI monitor",
                "metaKeyword" => "màn hình koorui, koorui monitor",
                "metaDescription" => "Màn hình KOORUI gaming giá rẻ, chất lượng tốt."
            ],
            [
                "parentId" => 14,
                "name" => "Phần mềm Văn phòng",
                "description" => "Phần mềm bản quyền văn phòng",
                "order" => 2,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Phần mềm - Software văn phòng",
                "metaKeyword" => "phần mềm, software",
                "metaDescription" => "Phần mềm bản quyền cho văn phòng."
            ],
            [
                "parentId" => 14,
                "name" => "Decor bàn làm việc",
                "description" => "Đồ trang trí bàn làm việc",
                "order" => 3,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Decor bàn làm việc - Desk decoration",
                "metaKeyword" => "decor bàn, trang trí bàn",
                "metaDescription" => "Đồ decor trang trí bàn làm việc đẹp mắt, hiện đại."
            ],
            [
                "parentId" => 15,
                "name" => "Thiết bị phát sóng WiFi",
                "description" => "Router WiFi tốc độ cao",
                "order" => 1,
                "publish" => 2,
                "isHot" => true,
                "metaTitle" => "Thiết bị phát sóng WiFi - WiFi Router",
                "metaKeyword" => "router wifi, wifi router, bộ phát wifi",
                "metaDescription" => "Router WiFi tốc độ cao, phủ sóng rộng cho gia đình và văn phòng."
            ],
            [
                "parentId" => 15,
                "name" => "Bộ phát wifi di động",
                "description" => "Bộ phát WiFi 4G di động",
                "order" => 2,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Bộ phát wifi di động - Mobile WiFi",
                "metaKeyword" => "bộ phát wifi di động, mobile wifi, 4g wifi",
                "metaDescription" => "Bộ phát WiFi 4G di động tiện lợi khi đi du lịch."
            ],
            [
                "parentId" => 15,
                "name" => "Bộ kích sóng WiFi",
                "description" => "Bộ kích sóng mở rộng WiFi",
                "order" => 3,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Bộ kích sóng WiFi - WiFi Extender",
                "metaKeyword" => "bộ kích sóng wifi, wifi extender",
                "metaDescription" => "Bộ kích sóng WiFi mở rộng phạm vi phủ sóng."
            ],
            [
                "parentId" => 15,
                "name" => "Xem tất cả thiết bị mạng",
                "description" => "Tất cả thiết bị mạng",
                "order" => 4,
                "publish" => 2,
                "isHot" => false,
                "metaTitle" => "Xem tất cả thiết bị mạng",
                "metaKeyword" => "thiết bị mạng, network devices",
                "metaDescription" => "Xem tất cả thiết bị mạng: router, switch, extender."
            ]
        ];

        $userId = 1; // Default Admin ID

        if (ProductCatalogue::count() > 0) {
            $this->command->info('Product catalogues already seeded. Skipping.');
            return;
        }

        // Insert Parents
        foreach ($parents as $index => $item) {
            ProductCatalogue::create([
                'name' => $item['name'],
                'slug' => Str::slug($item['name']),
                'description' => $item['description'],
                'image' => null, // No image in JSON
                'order' => $item['order'],
                'is_hot' => $item['isHot'],
                'publish' => $item['publish'],
                'parent_id' => 0, // Root
                'user_id' => $userId,
                'meta_title' => $item['metaTitle'] ?? null,
                'meta_keyword' => $item['metaKeyword'] ?? $item['meta_keyword'] ?? null,
                'meta_description' => $item['metaDescription'] ?? null,
                'level' => 1,
            ]);
        }

        // Insert Children
        foreach ($children as $item) {
            ProductCatalogue::create([
                'name' => $item['name'],
                'slug' => Str::slug($item['name']) . '-' . uniqid(), // Append uniqid to avoid duplicates if names are same
                'description' => $item['description'],
                'image' => null,
                'order' => $item['order'],
                'is_hot' => $item['isHot'],
                'publish' => $item['publish'],
                'parent_id' => $item['parentId'],
                'user_id' => $userId,
                'meta_title' => $item['metaTitle'] ?? null,
                'meta_keyword' => $item['metaKeyword'] ?? $item['meta_keyword'] ?? null,
                'meta_description' => $item['metaDescription'] ?? null,
                'level' => 2,
            ]);
        }
    }
}
