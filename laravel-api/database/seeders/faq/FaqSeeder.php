<?php

namespace Database\Seeders\Faq;

use Illuminate\Database\Seeder;
use App\Models\Faq;
use Illuminate\Support\Str;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                "question" => "Cửa hàng có những loại sản phẩm công nghệ nào?",
                "answer" => "<p>Cửa hàng chúng tôi cung cấp đa dạng các sản phẩm công nghệ bao gồm:</p><ul><li><strong>Điện thoại và tablet:</strong> Smartphone, máy tính bảng các hãng Apple, Samsung, Xiaomi, OPPO...</li><li><strong>Laptop và PC:</strong> Laptop văn phòng, gaming, workstation và máy tính để bàn</li><li><strong>Phụ kiện:</strong> Tai nghe, sạc dự phòng, ốp lưng, cáp sạc, bàn phím, chuột...</li><li><strong>Thiết bị âm thanh:</strong> Loa bluetooth, tai nghe gaming, headphone cao cấp</li><li><strong>Đồng hồ thông minh:</strong> Apple Watch, Samsung Galaxy Watch, các hãng khác</li></ul><p>Tất cả sản phẩm đều là hàng chính hãng, có đầy đủ tem bảo hành và hóa đơn VAT.</p>",
                "sort_order" => 1,
                "is_featured" => 1,
                "faq_catalogue_id" => 1,
                "publish" => 2
            ],
            [
                "question" => "Sản phẩm có phải hàng chính hãng không?",
                "answer" => "<p>Chúng tôi cam kết <strong>100% sản phẩm chính hãng</strong> với các đặc điểm sau:</p><ul><li>Nguồn gốc nhập khẩu rõ ràng từ các nhà phân phối ủy quyền chính thức</li><li>Có đầy đủ tem chống hàng giả và tem bảo hành của hãng</li><li>Kèm theo hóa đơn VAT đầy đủ khi mua hàng</li><li>Được bảo hành theo chính sách của hãng sản xuất</li></ul><p>Nếu phát hiện sản phẩm không chính hãng, chúng tôi sẽ <strong>hoàn tiền 200% giá trị sản phẩm</strong>.</p>",
                "sort_order" => 2,
                "is_featured" => 1,
                "faq_catalogue_id" => 1,
                "publish" => 2
            ],
            [
                "question" => "Làm thế nào để kiểm tra tình trạng còn hàng của sản phẩm?",
                "answer" => "<p>Bạn có thể kiểm tra tình trạng hàng hóa qua nhiều cách:</p><ol><li><strong>Trên trang sản phẩm:</strong> Ngay dưới tên sản phẩm sẽ hiển thị trạng thái \"Còn hàng\" hoặc \"Hết hàng\"</li><li><strong>Gọi hotline:</strong> Liên hệ 1900.xxxx để nhân viên kiểm tra kho hàng trực tiếp</li><li><strong>Chat online:</strong> Sử dụng tính năng chat trực tuyến trên website</li><li><strong>Kiểm tra theo chi nhánh:</strong> Chọn chi nhánh gần nhất để xem tình trạng hàng tại từng cửa hàng</li></ol><p>Lưu ý: Tình trạng hàng hóa được cập nhật theo thời gian thực, đôi khi có thể thay đổi nhanh đối với sản phẩm hot.</p>",
                "sort_order" => 3,
                "is_featured" => 0,
                "faq_catalogue_id" => 1,
                "publish" => 2
            ],
            [
                "question" => "Tôi có thể xem trực tiếp sản phẩm trước khi mua không?",
                "answer" => "<p>Hoàn toàn có thể! Chúng tôi khuyến khích khách hàng đến trực tiếp showroom để:</p><ul><li><strong>Xem và trải nghiệm sản phẩm:</strong> Tất cả sản phẩm đều được trưng bày và có thể thử nghiệm</li><li><strong>Tư vấn trực tiếp:</strong> Đội ngũ tư vấn viên chuyên nghiệp sẽ hỗ trợ bạn chọn sản phẩm phù hợp</li><li><strong>So sánh nhiều sản phẩm:</strong> Dễ dàng so sánh các mẫu khác nhau cùng lúc</li><li><strong>Kiểm tra kỹ sản phẩm:</strong> Kiểm tra tình trạng máy, phụ kiện đầy đủ trước khi quyết định</li></ul><p>Xem địa chỉ chi tiết tại mục \"Hệ thống cửa hàng\" trên website.</p>",
                "sort_order" => 4,
                "is_featured" => 0,
                "faq_catalogue_id" => 1,
                "publish" => 2
            ],
            [
                "question" => "Tôi có thể đặt trước sản phẩm sắp ra mắt không?",
                "answer" => "<p>Có, chúng tôi nhận đặt trước các sản phẩm mới với các ưu đãi đặc biệt:</p><p><strong>Quy trình đặt trước:</strong></p><ol><li>Truy cập trang sản phẩm có nhãn \"Pre-order\"</li><li>Đặt cọc trước (thường từ 1-5 triệu tùy sản phẩm)</li><li>Nhận xác nhận đơn hàng qua email/SMS</li><li>Thanh toán phần còn lại khi nhận hàng</li></ol><p><strong>Ưu đãi khi đặt trước:</strong> Giảm giá từ 500.000đ - 3.000.000đ, ưu tiên giao hàng sớm nhất, tặng quà tri ân.</p>",
                "sort_order" => 5,
                "is_featured" => 0,
                "faq_catalogue_id" => 1,
                "publish" => 2
            ],
            [
                "question" => "Làm sao để so sánh nhiều sản phẩm với nhau?",
                "answer" => "<p>Website của chúng tôi có tính năng so sánh sản phẩm rất tiện lợi:</p><p><strong>Cách thực hiện:</strong></p><ol><li>Tìm và chọn các sản phẩm bạn muốn so sánh</li><li>Nhấn vào biểu tượng \"So sánh\" ở mỗi sản phẩm</li><li>Sau khi chọn đủ (tối đa 4 sản phẩm), nhấn \"Xem so sánh\"</li><li>Hệ thống sẽ hiển thị bảng so sánh chi tiết</li></ol><p><strong>Thông tin được so sánh:</strong> Thông số kỹ thuật, giá bán, khuyến mãi, đánh giá người dùng, ưu nhược điểm nổi bật.</p>",
                "sort_order" => 6,
                "is_featured" => 0,
                "faq_catalogue_id" => 1,
                "publish" => 2
            ],
            [
                "question" => "Sản phẩm cũ của tôi có được thu mua lại không?",
                "answer" => "<p>Có, chúng tôi có dịch vụ thu cũ đổi mới với giá hấp dẫn:</p><p><strong>Sản phẩm được thu mua:</strong> Điện thoại, laptop, tablet, đồng hồ thông minh, console game...</p><p><strong>Quy trình:</strong></p><ol><li>Định giá online qua tool \"Tra giá thu cũ\" trên website</li><li>Mang máy đến cửa hàng để kiểm tra chi tiết</li><li>Nhận tiền mặt hoặc trừ vào đơn hàng mới</li></ol><p><strong>Lưu ý:</strong> Máy cần còn nguyên vẹn, màn hình không bể. Có đầy đủ hộp và phụ kiện sẽ được giá cao hơn.</p>",
                "sort_order" => 7,
                "is_featured" => 0,
                "faq_catalogue_id" => 1,
                "publish" => 2
            ],
            [
                "question" => "Làm thế nào để đặt hàng trên website?",
                "answer" => "<p>Đặt hàng trực tuyến trên website rất đơn giản:</p><p><strong>Bước 1:</strong> Chọn sản phẩm và nhấn \"Thêm vào giỏ hàng\"</p><p><strong>Bước 2:</strong> Kiểm tra giỏ hàng, nhập mã giảm giá (nếu có)</p><p><strong>Bước 3:</strong> Điền thông tin giao hàng (họ tên, SĐT, địa chỉ)</p><p><strong>Bước 4:</strong> Chọn phương thức thanh toán (COD, chuyển khoản, ví điện tử, thẻ...)</p><p><strong>Bước 5:</strong> Xác nhận và hoàn tất đơn hàng</p><p>Sau khi đặt hàng thành công, bộ phận CSKH sẽ liên hệ xác nhận trong vòng 30 phút.</p>",
                "sort_order" => 1,
                "is_featured" => 1,
                "faq_catalogue_id" => 2,
                "publish" => 2
            ],
            [
                "question" => "Website hỗ trợ những phương thức thanh toán nào?",
                "answer" => "<p>Chúng tôi hỗ trợ đa dạng phương thức thanh toán:</p><ul><li><strong>COD:</strong> Thanh toán khi nhận hàng (đơn dưới 20 triệu)</li><li><strong>Chuyển khoản:</strong> Qua Internet Banking hoặc tại quầy</li><li><strong>Ví điện tử:</strong> Momo, ZaloPay, VNPay, ShopeePay</li><li><strong>Thẻ:</strong> Visa, Mastercard, JCB, ATM nội địa</li><li><strong>Trả góp:</strong> Qua thẻ tín dụng hoặc công ty tài chính (Home Credit, FE Credit...)</li></ul><p>Tất cả giao dịch đều được mã hóa và bảo mật tuyệt đối.</p>",
                "sort_order" => 2,
                "is_featured" => 1,
                "faq_catalogue_id" => 2,
                "publish" => 2
            ],
            [
                "question" => "Tôi có thể hủy hoặc thay đổi đơn hàng sau khi đặt không?",
                "answer" => "<p>Có, nhưng tùy thuộc vào tình trạng đơn hàng:</p><p><strong>Hủy đơn hàng:</strong></p><ul><li><strong>Trước khi xác nhận:</strong> Hủy miễn phí 100%</li><li><strong>Sau xác nhận nhưng chưa xuất kho:</strong> Hủy miễn phí, liên hệ hotline ngay</li><li><strong>Đã xuất kho:</strong> Cần liên hệ hotline, có thể mất phí vận chuyển</li></ul><p><strong>Thay đổi:</strong> Địa chỉ, SĐT, phương thức thanh toán có thể đổi nếu đơn chưa xuất kho.</p><p><strong>Liên hệ:</strong> Hotline 1900.xxxx (nhanh nhất), chat trực tuyến, hoặc email.</p>",
                "sort_order" => 3,
                "is_featured" => 0,
                "faq_catalogue_id" => 2,
                "publish" => 2
            ],
            [
                "question" => "Tôi có thể đặt hàng mà không cần tài khoản không?",
                "answer" => "<p>Có, bạn hoàn toàn có thể đặt hàng với tư cách khách mà không cần đăng ký tài khoản.</p><p><strong>Tuy nhiên, đăng ký tài khoản sẽ có nhiều lợi ích:</strong></p><ul><li>Không cần điền thông tin mỗi lần đặt hàng</li><li>Quản lý và theo dõi đơn hàng dễ dàng</li><li>Tích điểm thưởng cho mỗi đơn hàng</li><li>Nhận ưu đãi và thông báo độc quyền</li><li>Lưu danh sách sản phẩm yêu thích</li></ul><p>Đăng ký tài khoản hoàn toàn miễn phí và chỉ mất 1 phút.</p>",
                "sort_order" => 4,
                "is_featured" => 0,
                "faq_catalogue_id" => 2,
                "publish" => 2
            ],
            [
                "question" => "Mã giảm giá được áp dụng như thế nào?",
                "answer" => "<p>Mã giảm giá (voucher/coupon) giúp bạn tiết kiệm chi phí khi mua hàng:</p><p><strong>Cách sử dụng:</strong></p><ol><li>Thêm sản phẩm vào giỏ hàng</li><li>Tại trang thanh toán, tìm ô \"Nhập mã giảm giá\"</li><li>Nhập mã và nhấn \"Áp dụng\"</li><li>Số tiền giảm được trừ tự động</li></ol><p><strong>Lưu ý:</strong></p><ul><li>Mỗi đơn hàng chỉ áp dụng 1 mã giảm giá</li><li>Kiểm tra điều kiện áp dụng (giá trị đơn tối thiểu, sản phẩm áp dụng...)</li><li>Mã có thời hạn sử dụng, cần dùng trước ngày hết hạn</li></ul>",
                "sort_order" => 5,
                "is_featured" => 0,
                "faq_catalogue_id" => 2,
                "publish" => 2
            ],
            [
                "question" => "Tôi có thể đặt hàng số lượng lớn không?",
                "answer" => "<p>Có, chúng tôi hỗ trợ đặt hàng số lượng lớn với nhiều ưu đãi đặc biệt:</p><p><strong>Đối tượng:</strong> Doanh nghiệp, công ty, trường học, tổ chức, cửa hàng bán lẻ...</p><p><strong>Ưu đãi:</strong></p><ul><li>Giá ưu đãi theo số lượng (càng nhiều càng rẻ)</li><li>Tư vấn và báo giá riêng</li><li>Hỗ trợ xuất hóa đơn VAT đỏ</li><li>Chính sách bảo hành đặc biệt</li><li>Giao hàng và lắp đặt miễn phí</li></ul><p><strong>Liên hệ:</strong> Email doanh nghiệp: b2b@example.com hoặc hotline: 1900.xxxx (nhấn phím 2).</p>",
                "sort_order" => 6,
                "is_featured" => 0,
                "faq_catalogue_id" => 2,
                "publish" => 2
            ],
            [
                "question" => "Đơn hàng của tôi sẽ được giao trong bao lâu?",
                "answer" => "<p>Thời gian giao hàng phụ thuộc vào địa chỉ của bạn:</p><p><strong>Nội thành Hà Nội và TP.HCM:</strong></p><ul><li>Giao hàng trong 2-4 giờ với đơn đặt trước 16h</li><li>Giao hàng trong ngày với đơn đặt sau 16h (giao trước 22h)</li></ul><p><strong>Ngoại thành và các tỉnh lân cận:</strong></p><ul><li>1-2 ngày làm việc</li></ul><p><strong>Các tỉnh thành khác:</strong></p><ul><li>2-4 ngày làm việc tùy khoảng cách</li><li>Vùng sâu, vùng xa: 4-7 ngày làm việc</li></ul><p><strong>Lưu ý:</strong> Thời gian trên là thời gian dự kiến. Bạn có thể theo dõi tình trạng đơn hàng qua mã vận đơn được gửi qua SMS/Email.</p>",
                "sort_order" => 1,
                "is_featured" => 1,
                "faq_catalogue_id" => 3,
                "publish" => 2
            ],
            [
                "question" => "Phí vận chuyển được tính như thế nào?",
                "answer" => "<p>Phí vận chuyển được tính dựa trên giá trị đơn hàng và khu vực giao hàng:</p><p><strong>Miễn phí vận chuyển:</strong></p><ul><li>Đơn hàng từ 500.000đ trở lên: Miễn phí toàn quốc</li><li>Đơn hàng dưới 500.000đ: Miễn phí nội thành HN và HCM</li></ul><p><strong>Phí vận chuyển tiêu chuẩn:</strong></p><ul><li>Nội thành HN, HCM: 30.000đ</li><li>Ngoại thành và tỉnh lân cận: 30.000đ - 50.000đ</li><li>Các tỉnh thành khác: 30.000đ - 80.000đ</li></ul><p>Phí vận chuyển chính xác sẽ được hiển thị khi bạn nhập địa chỉ giao hàng tại trang thanh toán.</p>",
                "sort_order" => 2,
                "is_featured" => 1,
                "faq_catalogue_id" => 3,
                "publish" => 2
            ],
            [
                "question" => "Tôi có thể chọn thời gian giao hàng không?",
                "answer" => "<p>Có, chúng tôi hỗ trợ bạn chọn khung giờ giao hàng phù hợp:</p><p><strong>Các khung giờ giao hàng:</strong></p><ul><li>Sáng: 8h00 - 12h00</li><li>Chiều: 13h00 - 18h00</li><li>Tối: 18h00 - 21h00</li></ul><p><strong>Cách chọn:</strong></p><ol><li>Tại trang thanh toán, chọn mục \"Thời gian giao hàng mong muốn\"</li><li>Chọn ngày và khung giờ bạn muốn nhận hàng</li><li>Ghi chú thêm nếu có yêu cầu đặc biệt</li></ol><p><strong>Lưu ý:</strong> Đây là thời gian dự kiến, shipper sẽ gọi điện trước 15-30 phút để xác nhận. Trong trường hợp đặc biệt, có thể giao sớm hoặc muộn hơn 1-2 giờ.</p>",
                "sort_order" => 3,
                "is_featured" => 0,
                "faq_catalogue_id" => 3,
                "publish" => 2
            ],
            [
                "question" => "Làm thế nào để theo dõi đơn hàng của tôi?",
                "answer" => "<p>Bạn có thể theo dõi tình trạng đơn hàng dễ dàng qua nhiều cách:</p><p><strong>1. Qua website:</strong></p><ul><li>Đăng nhập tài khoản</li><li>Vào mục \"Đơn hàng của tôi\"</li><li>Xem chi tiết và trạng thái đơn hàng</li></ul><p><strong>2. Qua mã vận đơn:</strong></p><ul><li>Nhận mã vận đơn qua SMS/Email sau khi đơn hàng xuất kho</li><li>Truy cập website đơn vị vận chuyển (Giao Hàng Nhanh, Giao Hàng Tiết Kiệm...)</li><li>Nhập mã để theo dõi</li></ul><p><strong>3. Liên hệ trực tiếp:</strong></p><ul><li>Gọi hotline: 1900.xxxx</li><li>Chat trực tuyến với CSKH</li></ul><p>Bạn sẽ nhận được thông báo qua SMS/Email khi đơn hàng thay đổi trạng thái.</p>",
                "sort_order" => 4,
                "is_featured" => 0,
                "faq_catalogue_id" => 3,
                "publish" => 2
            ],
            [
                "question" => "Nếu tôi không có nhà khi giao hàng thì sao?",
                "answer" => "<p>Nếu bạn không có nhà khi shipper đến giao hàng:</p><p><strong>Shipper sẽ:</strong></p><ol><li>Gọi điện thoại cho bạn trước khi đến (15-30 phút)</li><li>Nếu không liên lạc được, sẽ để lại tin nhắn/SMS</li><li>Chờ thêm 10-15 phút tại địa chỉ</li><li>Nếu vẫn không liên hệ được, đơn hàng sẽ được hoàn về kho</li></ol><p><strong>Giải pháp:</strong></p><ul><li><strong>Giao cho người khác:</strong> Bạn có thể ghi chú số điện thoại người nhận thay (gia đình, bảo vệ...)</li><li><strong>Đổi lịch giao hàng:</strong> Liên hệ hotline để hẹn giao lại (có thể mất thêm phí)</li><li><strong>Nhận tại bưu cục:</strong> Yêu cầu giữ hàng tại bưu cục gần nhất để đến lấy</li></ul>",
                "sort_order" => 5,
                "is_featured" => 0,
                "faq_catalogue_id" => 3,
                "publish" => 2
            ],
            [
                "question" => "Tôi có thể đổi địa chỉ giao hàng sau khi đặt không?",
                "answer" => "<p>Có, nhưng tùy thuộc vào tình trạng đơn hàng:</p><p><strong>Đơn hàng chưa xuất kho:</strong></p><ul><li>Có thể đổi địa chỉ miễn phí</li><li>Liên hệ ngay hotline: 1900.xxxx hoặc chat online</li><li>Cung cấp mã đơn hàng và địa chỉ mới</li></ul><p><strong>Đơn hàng đã xuất kho/đang giao:</strong></p><ul><li>Vẫn có thể đổi địa chỉ trong cùng khu vực giao hàng</li><li>Có thể phát sinh thêm phí vận chuyển nếu đổi sang khu vực xa hơn</li><li>Liên hệ càng sớm càng tốt để xử lý</li></ul><p><strong>Lưu ý:</strong> Một số trường hợp shipper đã rất gần địa chỉ cũ, việc đổi địa chỉ có thể không kịp thời.</p>",
                "sort_order" => 6,
                "is_featured" => 0,
                "faq_catalogue_id" => 3,
                "publish" => 2
            ],
            [
                "question" => "Chính sách bảo hành như thế nào?",
                "answer" => "<p>Tất cả sản phẩm đều được bảo hành chính hãng theo tiêu chuẩn của nhà sản xuất:</p><p><strong>Thời gian bảo hành:</strong></p><ul><li><strong>Điện thoại, Tablet:</strong> 12 tháng</li><li><strong>Laptop, PC:</strong> 12-24 tháng (tùy hãng và dòng sản phẩm)</li><li><strong>Phụ kiện:</strong> 3-12 tháng (tùy loại)</li><li><strong>Tai nghe, Loa:</strong> 6-12 tháng</li></ul><p><strong>Điều kiện bảo hành:</strong></p><ul><li>Sản phẩm còn nguyên tem bảo hành</li><li>Lỗi do nhà sản xuất (lỗi kỹ thuật, linh kiện)</li><li>Không có dấu hiệu va đập, rơi vỡ, vào nước</li><li>Chưa qua sửa chữa tại nơi không ủy quyền</li></ul><p><strong>Quy trình bảo hành:</strong></p><ol><li>Mang sản phẩm và phiếu bảo hành đến trung tâm bảo hành</li><li>Nhân viên kiểm tra và tiếp nhận</li><li>Sửa chữa hoặc đổi mới trong 7-15 ngày</li><li>Nhận lại sản phẩm</li></ol>",
                "sort_order" => 1,
                "is_featured" => 1,
                "faq_catalogue_id" => 4,
                "publish" => 2
            ],
            [
                "question" => "Trung tâm bảo hành ở đâu?",
                "answer" => "<p>Chúng tôi có hệ thống trung tâm bảo hành rộng khắp cả nước:</p><p><strong>Bảo hành tại cửa hàng:</strong></p><ul><li>Tất cả các chi nhánh đều hỗ trợ tiếp nhận bảo hành</li><li>Xử lý nhanh các lỗi đơn giản ngay tại chỗ</li></ul><p><strong>Trung tâm bảo hành ủy quyền:</strong></p><ul><li>Hà Nội: 5+ trung tâm</li><li>TP.HCM: 8+ trung tâm</li><li>Các tỉnh thành khác: 20+ trung tâm</li></ul><p><strong>Bảo hành hãng:</strong></p><ul><li>Apple: Trung tâm bảo hành ủy quyền Apple (AASP)</li><li>Samsung, Xiaomi, OPPO...: Trung tâm chính hãng của từng hãng</li></ul><p>Danh sách chi tiết địa chỉ xem tại trang \"Trung tâm bảo hành\" trên website hoặc gọi hotline: 1900.xxxx.</p>",
                "sort_order" => 2,
                "is_featured" => 0,
                "faq_catalogue_id" => 4,
                "publish" => 2
            ],
            [
                "question" => "Sản phẩm bị lỗi có được đổi mới không?",
                "answer" => "<p>Có, sản phẩm lỗi sẽ được xử lý theo chính sách đổi trả của chúng tôi:</p><p><strong>Đổi mới trong 30 ngày đầu:</strong></p><ul><li>Sản phẩm lỗi do nhà sản xuất được đổi mới 100%</li><li>Không tính phí, không cần lý do</li><li>Máy đổi cùng mẫu, cùng màu (hoặc tương đương nếu hết hàng)</li></ul><p><strong>Đổi mới trong thời gian bảo hành:</strong></p><ul><li>Nếu sản phẩm lỗi 3 lần trở lên: Đổi mới hoặc hoàn tiền 100%</li><li>Nếu không sửa được: Đổi sản phẩm tương đương</li></ul><p><strong>Quy trình đổi mới:</strong></p><ol><li>Mang sản phẩm và hóa đơn đến cửa hàng</li><li>Nhân viên kiểm tra lỗi</li><li>Xác nhận đổi mới ngay hoặc trong 1-3 ngày</li></ol>",
                "sort_order" => 3,
                "is_featured" => 0,
                "faq_catalogue_id" => 4,
                "publish" => 2
            ],
            [
                "question" => "Tôi có thể yêu cầu hỗ trợ kỹ thuật qua điện thoại không?",
                "answer" => "<p>Có, chúng tôi cung cấp dịch vụ hỗ trợ kỹ thuật miễn phí qua nhiều kênh:</p><p><strong>Hotline hỗ trợ kỹ thuật:</strong></p><ul><li>Số điện thoại: 1900.xxxx (nhấn phím 3)</li><li>Thời gian: 8h00 - 22h00 hàng ngày</li><li>Hỗ trợ: Cài đặt, sử dụng, xử lý lỗi cơ bản</li></ul><p><strong>Hỗ trợ trực tuyến:</strong></p><ul><li>Chat trực tiếp trên website</li><li>Zalo/Facebook Messenger</li><li>Email: support@example.com</li><li>TeamViewer: Hỗ trợ từ xa cho laptop/PC</li></ul><p><strong>Hỗ trợ tại nhà:</strong></p><ul><li>Dịch vụ có phí, áp dụng cho các trường hợp phức tạp</li><li>Đội ngũ kỹ thuật viên chuyên nghiệp</li><li>Đặt lịch qua hotline hoặc website</li></ul>",
                "sort_order" => 4,
                "is_featured" => 0,
                "faq_catalogue_id" => 4,
                "publish" => 2
            ],
            [
                "question" => "Chi phí sửa chữa ngoài bảo hành là bao nhiêu?",
                "answer" => "<p>Đối với các trường hợp không thuộc diện bảo hành (rơi vỡ, vào nước, hết hạn bảo hành...):</p><p><strong>Báo giá miễn phí:</strong></p><ul><li>Mang máy đến cửa hàng để kiểm tra</li><li>Nhân viên kỹ thuật báo giá chi tiết</li><li>Bạn quyết định có sửa hay không</li><li>Không tính phí kiểm tra và báo giá</li></ul><p><strong>Chi phí tham khảo:</strong></p><ul><li>Thay màn hình điện thoại: 500.000đ - 3.000.000đ</li><li>Thay pin: 200.000đ - 800.000đ</li><li>Sửa main, IC: 300.000đ - 2.000.000đ</li><li>Vệ sinh, nâng cấp laptop: 200.000đ - 500.000đ</li></ul><p><strong>Bảo hành sau sửa chữa:</strong> 3-6 tháng cho linh kiện thay thế.</p>",
                "sort_order" => 5,
                "is_featured" => 0,
                "faq_catalogue_id" => 4,
                "publish" => 2
            ],
            [
                "question" => "Tôi có thể mua thêm gói bảo hành mở rộng không?",
                "answer" => "<p>Có, chúng tôi cung cấp các gói bảo hành mở rộng với nhiều quyền lợi:</p><p><strong>Gói VIP Care:</strong></p><ul><li>Thời hạn: Thêm 12 tháng</li><li>Bảo hành rơi vỡ, vào nước (tối đa 2 lần/năm)</li><li>Ưu tiên sửa chữa nhanh (24-48h)</li><li>Đổi máy mới nếu không sửa được</li><li>Giá: 5-15% giá trị sản phẩm</li></ul><p><strong>Gói Standard Care:</strong></p><ul><li>Thời hạn: Thêm 12 tháng</li><li>Chỉ bảo hành lỗi kỹ thuật</li><li>Hỗ trợ kỹ thuật ưu tiên</li><li>Giá: 3-8% giá trị sản phẩm</li></ul><p><strong>Mua gói:</strong> Có thể mua cùng lúc với sản phẩm hoặc trong vòng 30 ngày sau khi mua.</p>",
                "sort_order" => 6,
                "is_featured" => 0,
                "faq_catalogue_id" => 4,
                "publish" => 2
            ],
            [
                "question" => "Tôi có thể đổi trả sản phẩm trong bao lâu?",
                "answer" => "<p>Chính sách đổi trả linh hoạt giúp bạn yên tâm mua sắm:</p><p><strong>Đổi trả trong 7 ngày:</strong></p><ul><li>Áp dụng cho tất cả sản phẩm</li><li>Sản phẩm còn nguyên vẹn, đầy đủ hộp và phụ kiện</li><li>Chưa qua sử dụng hoặc có dấu hiệu sử dụng tối thiểu</li><li>Hoàn tiền 100% hoặc đổi sản phẩm khác</li></ul><p><strong>Đổi trả trong 30 ngày (sản phẩm lỗi):</strong></p><ul><li>Sản phẩm có lỗi kỹ thuật do nhà sản xuất</li><li>Đổi mới 100% không mất phí</li></ul><p><strong>Điều kiện:</strong></p><ul><li>Có hóa đơn mua hàng</li><li>Tem bảo hành còn nguyên vẹn</li><li>Sản phẩm không bị trầy xước, móp méo nghiêm trọng</li></ul>",
                "sort_order" => 1,
                "is_featured" => 1,
                "faq_catalogue_id" => 5,
                "publish" => 2
            ],
            [
                "question" => "Quy trình đổi trả sản phẩm như thế nào?",
                "answer" => "<p>Quy trình đổi trả đơn giản và nhanh chóng:</p><p><strong>Bước 1: Liên hệ</strong></p><ul><li>Gọi hotline: 1900.xxxx hoặc chat online</li><li>Thông báo mã đơn hàng và lý do đổi trả</li><li>Nhân viên sẽ tư vấn và hướng dẫn</li></ul><p><strong>Bước 2: Gửi lại sản phẩm</strong></p><ul><li><strong>Tại cửa hàng:</strong> Mang sản phẩm, hóa đơn đến cửa hàng gần nhất</li><li><strong>Qua đường bưu điện:</strong> Đóng gói cẩn thận và gửi về địa chỉ được hướng dẫn (chúng tôi chịu phí vận chuyển nếu sản phẩm lỗi)</li></ul><p><strong>Bước 3: Kiểm tra</strong></p><ul><li>Nhân viên kiểm tra tình trạng sản phẩm</li><li>Xác nhận đủ điều kiện đổi trả</li></ul><p><strong>Bước 4: Hoàn tất</strong></p><ul><li>Nhận sản phẩm mới hoặc tiền hoàn (qua chuyển khoản)</li><li>Thời gian xử lý: 1-3 ngày làm việc</li></ul>",
                "sort_order" => 2,
                "is_featured" => 1,
                "faq_catalogue_id" => 5,
                "publish" => 2
            ],
            [
                "question" => "Tôi có được hoàn tiền không?",
                "answer" => "<p>Có, bạn được hoàn tiền trong các trường hợp sau:</p><p><strong>Hoàn tiền 100%:</strong></p><ul><li>Sản phẩm lỗi và không còn hàng để đổi</li><li>Đổi trả trong 7 ngày và bạn yêu cầu hoàn tiền</li><li>Shop giao sai sản phẩm, sai màu, sai mẫu</li><li>Đơn hàng bị hủy do shop không có hàng</li></ul><p><strong>Hình thức hoàn tiền:</strong></p><ul><li>Chuyển khoản ngân hàng (2-5 ngày làm việc)</li><li>Tiền mặt tại cửa hàng (nếu mua trực tiếp)</li><li>Hoàn vào ví điện tử (nếu thanh toán qua ví)</li><li>Hoàn vào thẻ tín dụng (5-14 ngày tùy ngân hàng)</li></ul><p><strong>Lưu ý:</strong> Phí vận chuyển (nếu có) sẽ được hoàn lại nếu lỗi do shop.</p>",
                "sort_order" => 3,
                "is_featured" => 0,
                "faq_catalogue_id" => 5,
                "publish" => 2
            ],
            [
                "question" => "Sản phẩm đã qua sử dụng có được đổi trả không?",
                "answer" => "<p>Tùy thuộc vào tình trạng sử dụng và thời gian:</p><p><strong>Được chấp nhận:</strong></p><ul><li>Sản phẩm đã mở hộp để kiểm tra nhưng chưa sử dụng thực sự</li><li>Có dấu hiệu sử dụng nhẹ nhưng vẫn còn mới 95% trở lên</li><li>Trong vòng 7 ngày và không có lỗi do người dùng</li></ul><p><strong>Không được chấp nhận:</strong></p><ul><li>Có dấu hiệu sử dụng nhiều, trầy xước rõ ràng</li><li>Thiếu phụ kiện, hộp bị hư hỏng nặng</li><li>Có lỗi do sử dụng không đúng cách</li></ul><p><strong>Lưu ý:</strong> Nhân viên sẽ kiểm tra cẩn thận và quyết định cuối cùng dựa trên tình trạng thực tế.</p>",
                "sort_order" => 4,
                "is_featured" => 0,
                "faq_catalogue_id" => 5,
                "publish" => 2
            ],
            [
                "question" => "Tôi có thể đổi sang sản phẩm khác không?",
                "answer" => "<p>Có, bạn có thể đổi sang sản phẩm khác với các điều kiện:</p><p><strong>Đổi sản phẩm cùng giá trị:</strong></p><ul><li>Đổi miễn phí trong 7 ngày</li><li>Đổi sang màu khác, dung lượng khác, hoặc mẫu khác cùng giá</li></ul><p><strong>Đổi sang sản phẩm giá cao hơn:</strong></p><ul><li>Bù thêm phần chênh lệch</li><li>Áp dụng khuyến mãi mới nếu có</li></ul><p><strong>Đổi sang sản phẩm giá thấp hơn:</strong></p><ul><li>Hoàn lại phần chênh lệch (trừ phí xử lý 5% nếu sản phẩm đã sử dụng)</li><li>Hoặc nhận voucher mua hàng lần sau</li></ul><p><strong>Điều kiện:</strong> Sản phẩm cũ phải đủ điều kiện đổi trả theo chính sách.</p>",
                "sort_order" => 5,
                "is_featured" => 0,
                "faq_catalogue_id" => 5,
                "publish" => 2
            ],
            [
                "question" => "Chi phí vận chuyển khi đổi trả do ai chịu?",
                "answer" => "<p>Chi phí vận chuyển được xác định dựa trên nguyên nhân đổi trả:</p><p><strong>Shop chịu phí:</strong></p><ul><li>Sản phẩm lỗi do nhà sản xuất</li><li>Giao sai sản phẩm, sai màu, sai mẫu</li><li>Sản phẩm bị hư hỏng trong quá trình vận chuyển</li><li>Thiếu phụ kiện hoặc không đúng mô tả</li></ul><p><strong>Khách hàng chịu phí:</strong></p><ul><li>Đổi trả do không thích, không vừa ý (trong 7 ngày)</li><li>Đổi sang sản phẩm khác do ý muốn cá nhân</li><li>Đặt nhầm sản phẩm</li></ul><p><strong>Miễn phí:</strong></p><ul><li>Nếu bạn trực tiếp mang sản phẩm đến cửa hàng</li><li>Đổi trả tại thời điểm giao hàng (từ chối nhận)</li></ul>",
                "sort_order" => 6,
                "is_featured" => 0,
                "faq_catalogue_id" => 5,
                "publish" => 2
            ],
            [
                "question" => "Làm thế nào để đăng ký tài khoản?",
                "answer" => "<p>Đăng ký tài khoản rất đơn giản và nhanh chóng:</p><p><strong>Cách 1: Đăng ký bằng email/số điện thoại</strong></p><ol><li>Nhấn nút \"Đăng ký\" ở góc trên cùng website</li><li>Nhập email hoặc số điện thoại</li><li>Tạo mật khẩu (tối thiểu 6 ký tự)</li><li>Nhập mã xác nhận được gửi qua email/SMS</li><li>Hoàn tất đăng ký</li></ol><p><strong>Cách 2: Đăng ký nhanh qua mạng xã hội</strong></p><ul><li>Nhấn \"Đăng nhập với Facebook\" hoặc \"Đăng nhập với Google\"</li><li>Cấp quyền truy cập</li><li>Tài khoản được tạo tự động</li></ul><p><strong>Lợi ích khi có tài khoản:</strong></p><ul><li>Đặt hàng nhanh hơn</li><li>Theo dõi đơn hàng dễ dàng</li><li>Tích điểm thưởng</li><li>Nhận thông báo khuyến mãi độc quyền</li></ul>",
                "sort_order" => 1,
                "is_featured" => 1,
                "faq_catalogue_id" => 6,
                "publish" => 2
            ],
            [
                "question" => "Tôi quên mật khẩu, phải làm sao?",
                "answer" => "<p>Đừng lo lắng, bạn có thể khôi phục mật khẩu dễ dàng:</p><p><strong>Các bước khôi phục:</strong></p><ol><li>Nhấn \"Quên mật khẩu\" tại trang đăng nhập</li><li>Nhập email hoặc số điện thoại đã đăng ký</li><li>Kiểm tra email/SMS để nhận mã xác nhận</li><li>Nhập mã xác nhận</li><li>Tạo mật khẩu mới</li><li>Đăng nhập với mật khẩu mới</li></ol><p><strong>Lưu ý:</strong></p><ul><li>Mã xác nhận có hiệu lực trong 15 phút</li><li>Kiểm tra cả hộp thư spam nếu không thấy email</li><li>Nếu không nhận được mã, thử lại sau 1 phút</li></ul><p><strong>Vẫn không khôi phục được?</strong> Liên hệ hotline: 1900.xxxx để được hỗ trợ trực tiếp.</p>",
                "sort_order" => 2,
                "is_featured" => 0,
                "faq_catalogue_id" => 6,
                "publish" => 2
            ],
            [
                "question" => "Làm thế nào để cập nhật thông tin tài khoản?",
                "answer" => "<p>Bạn có thể cập nhật thông tin tài khoản bất cứ lúc nào:</p><p><strong>Các bước thực hiện:</strong></p><ol><li>Đăng nhập vào tài khoản</li><li>Nhấn vào tên tài khoản ở góc trên cùng</li><li>Chọn \"Thông tin tài khoản\" hoặc \"Cài đặt\"</li><li>Cập nhật các thông tin cần thiết</li><li>Nhấn \"Lưu thay đổi\"</li></ol><p><strong>Thông tin có thể cập nhật:</strong></p><ul><li>Họ tên</li><li>Ngày sinh</li><li>Giới tính</li><li>Địa chỉ email</li><li>Số điện thoại</li><li>Địa chỉ giao hàng mặc định</li><li>Mật khẩu</li></ul><p><strong>Lưu ý:</strong> Khi đổi email hoặc số điện thoại, bạn cần xác nhận bằng mã OTP.</p>",
                "sort_order" => 3,
                "is_featured" => 0,
                "faq_catalogue_id" => 6,
                "publish" => 2
            ],
            [
                "question" => "Điểm thưởng hoạt động như thế nào?",
                "answer" => "<p>Chương trình điểm thưởng giúp bạn tiết kiệm chi phí khi mua sắm:</p><p><strong>Cách tích điểm:</strong></p><ul><li>Mỗi 10.000đ chi tiêu = 1 điểm</li><li>Mua hàng lần đầu: Thưởng 50 điểm</li><li>Viết đánh giá sản phẩm: 10-20 điểm/đánh giá</li><li>Giới thiệu bạn bè: 100 điểm khi bạn bè mua hàng lần đầu</li></ul><p><strong>Cách sử dụng điểm:</strong></p><ul><li>100 điểm = 10.000đ giảm giá</li><li>Áp dụng trực tiếp khi thanh toán</li><li>Tối đa dùng 50% tổng giá trị đơn hàng</li></ul><p><strong>Hạng thành viên:</strong></p><ul><li><strong>Bạc:</strong> 0-499 điểm (tích điểm cơ bản)</li><li><strong>Vàng:</strong> 500-1999 điểm (x1.2 điểm, ưu đãi đặc biệt)</li><li><strong>Kim cương:</strong> 2000+ điểm (x1.5 điểm, ưu đãi VIP)</li></ul>",
                "sort_order" => 4,
                "is_featured" => 0,
                "faq_catalogue_id" => 6,
                "publish" => 2
            ],
            [
                "question" => "Làm thế nào để xem lịch sử mua hàng?",
                "answer" => "<p>Xem lịch sử mua hàng giúp bạn quản lý chi tiêu và tra cứu bảo hành dễ dàng:</p><p><strong>Các bước xem lịch sử:</strong></p><ol><li>Đăng nhập tài khoản</li><li>Vào mục \"Đơn hàng của tôi\" hoặc \"Lịch sử mua hàng\"</li><li>Xem danh sách tất cả đơn hàng</li></ol><p><strong>Thông tin hiển thị:</strong></p><ul><li>Mã đơn hàng</li><li>Ngày đặt hàng</li><li>Sản phẩm đã mua</li><li>Tổng tiền</li><li>Trạng thái đơn hàng</li><li>Phương thức thanh toán</li></ul><p><strong>Tính năng:</strong></p><ul><li><strong>Lọc:</strong> Theo thời gian, trạng thái</li><li><strong>Tìm kiếm:</strong> Theo mã đơn, tên sản phẩm</li><li><strong>Mua lại:</strong> Đặt lại đơn hàng cũ chỉ với 1 cú nhấp chuột</li><li><strong>In hóa đơn:</strong> Tải và in hóa đơn VAT</li></ul>",
                "sort_order" => 5,
                "is_featured" => 0,
                "faq_catalogue_id" => 6,
                "publish" => 2
            ],
            [
                "question" => "Tôi có thể xóa tài khoản không?",
                "answer" => "<p>Có, bạn có quyền xóa tài khoản của mình bất cứ lúc nào:</p><p><strong>Cách xóa tài khoản:</strong></p><ol><li>Đăng nhập tài khoản</li><li>Vào \"Cài đặt tài khoản\"</li><li>Chọn \"Xóa tài khoản\"</li><li>Xác nhận qua email/SMS</li><li>Tài khoản sẽ bị xóa vĩnh viễn sau 30 ngày</li></ol><p><strong>Lưu ý quan trọng:</strong></p><ul><li><strong>Không thể khôi phục:</strong> Sau 30 ngày, dữ liệu sẽ bị xóa vĩnh viễn</li><li><strong>Điểm thưởng:</strong> Tất cả điểm thưởng sẽ bị mất</li><li><strong>Đơn hàng:</strong> Không thể tra cứu lịch sử đơn hàng</li><li><strong>Bảo hành:</strong> Vẫn được bảo hành nếu có hóa đơn</li></ul><p><strong>Thay vì xóa:</strong> Bạn có thể tạm ngưng nhận email marketing trong phần cài đặt.</p>",
                "sort_order" => 6,
                "is_featured" => 0,
                "faq_catalogue_id" => 6,
                "publish" => 2
            ],
            [
                "question" => "Hiện tại có chương trình khuyến mãi nào không?",
                "answer" => "<p>Chúng tôi thường xuyên có các chương trình khuyến mãi hấp dẫn:</p><p><strong>Khuyến mãi hiện tại:</strong></p><ul><li><strong>Flash Sale:</strong> Giảm đến 50% vào 9h, 12h, 20h hàng ngày</li><li><strong>Mã giảm giá:</strong> Giảm 200.000đ cho đơn từ 2 triệu</li><li><strong>Trả góp 0%:</strong> Áp dụng cho sản phẩm từ 3 triệu trở lên</li><li><strong>Tặng phụ kiện:</strong> Khi mua điện thoại, laptop</li></ul><p><strong>Cách cập nhật khuyến mãi:</strong></p><ul><li>Xem trang \"Khuyến mãi\" trên website</li><li>Đăng ký nhận email/SMS thông báo</li><li>Theo dõi fanpage Facebook</li><li>Tải app di động để nhận thông báo đẩy</li></ul><p><strong>Khuyến mãi đặc biệt:</strong></p><ul><li>Sinh nhật cửa hàng: Tháng 8</li><li>Black Friday: Tháng 11</li><li>Tết Nguyên Đán: Tháng 1-2</li></ul>",
                "sort_order" => 1,
                "is_featured" => 1,
                "faq_catalogue_id" => 7,
                "publish" => 2
            ],
            [
                "question" => "Làm thế nào để nhận mã giảm giá?",
                "answer" => "<p>Có nhiều cách để bạn nhận được mã giảm giá:</p><p><strong>1. Đăng ký nhận tin:</strong></p><ul><li>Đăng ký email newsletter để nhận mã độc quyền</li><li>Thành viên mới nhận ngay mã giảm 10%</li></ul><p><strong>2. Theo dõi mạng xã hội:</strong></p><ul><li>Facebook, Instagram: Phát mã định kỳ</li><li>Zalo Official Account: Mã độc quyền cho người theo dõi</li></ul><p><strong>3. Tích điểm thưởng:</strong></p><ul><li>Đổi điểm lấy mã giảm giá</li><li>100 điểm = mã 10.000đ</li></ul><p><strong>4. Sự kiện đặc biệt:</strong></p><ul><li>Sinh nhật khách hàng: Mã giảm 15-20%</li><li>Giới thiệu bạn bè: Cả 2 đều nhận mã</li></ul><p><strong>5. Mua sắm thường xuyên:</strong></p><ul><li>Khách hàng thân thiết nhận mã VIP độc quyền</li></ul>",
                "sort_order" => 2,
                "is_featured" => 0,
                "faq_catalogue_id" => 7,
                "publish" => 2
            ],
            [
                "question" => "Combo sản phẩm có giá tốt hơn không?",
                "answer" => "<p>Có, mua combo sản phẩm giúp bạn tiết kiệm đáng kể:</p><p><strong>Ưu điểm mua combo:</strong></p><ul><li>Giảm giá từ 10-30% so với mua lẻ</li><li>Sản phẩm được chọn lọc kỹ càng, phù hợp với nhau</li><li>Tiết kiệm thời gian lựa chọn</li><li>Miễn phí vận chuyển (combo từ 2 triệu)</li></ul><p><strong>Các combo phổ biến:</strong></p><ul><li><strong>Combo sinh viên:</strong> Laptop + Chuột + Balo (Giảm 15%)</li><li><strong>Combo gaming:</strong> PC + Màn hình + Bàn phím chuột (Giảm 20%)</li><li><strong>Combo bảo vệ:</strong> Điện thoại + Ốp lưng + Kính cường lực (Giảm 10%)</li><li><strong>Combo gia đình:</strong> Smart TV + Loa soundbar (Giảm 25%)</li></ul><p>Xem thêm combo tại trang \"Combo khuyến mãi\" trên website.</p>",
                "sort_order" => 3,
                "is_featured" => 0,
                "faq_catalogue_id" => 7,
                "publish" => 2
            ],
            [
                "question" => "Chương trình tích điểm có ưu đãi gì?",
                "answer" => "<p>Chương trình tích điểm mang lại nhiều lợi ích cho khách hàng thân thiết:</p><p><strong>Cách tích điểm:</strong></p><ul><li>10.000đ = 1 điểm</li><li>Sinh nhật: x2 điểm</li><li>Sản phẩm đặc biệt: x3 điểm</li></ul><p><strong>Quyền lợi theo hạng:</strong></p><ul><li><strong>Hạng Bạc (0-499 điểm):</strong> Tích điểm chuẩn, giảm 5% sinh nhật</li><li><strong>Hạng Vàng (500-1999 điểm):</strong> x1.2 điểm, giảm 10% sinh nhật, ưu tiên hỗ trợ</li><li><strong>Hạng Kim Cương (2000+ điểm):</strong> x1.5 điểm, giảm 15% sinh nhật, mã giảm độc quyền, miễn phí vận chuyển mọi đơn</li></ul><p><strong>Đổi điểm:</strong></p><ul><li>100 điểm = 10.000đ giảm giá</li><li>Voucher phụ kiện, dịch vụ</li><li>Quà tặng đặc biệt</li></ul>",
                "sort_order" => 4,
                "is_featured" => 0,
                "faq_catalogue_id" => 7,
                "publish" => 2
            ],
            [
                "question" => "Có ưu đãi đặc biệt cho sinh viên không?",
                "answer" => "<p>Có, chúng tôi có chương trình ưu đãi riêng dành cho sinh viên:</p><p><strong>Ưu đãi sinh viên:</strong></p><ul><li>Giảm thêm 5-10% cho các sản phẩm laptop, tablet</li><li>Combo sinh viên giảm đến 20%</li><li>Trả góp 0% không cần thẻ tín dụng</li><li>Tặng phần mềm học tập (Office, Antivirus...)</li><li>Ưu tiên hỗ trợ kỹ thuật</li></ul><p><strong>Điều kiện:</strong></p><ul><li>Xuất trình thẻ sinh viên còn hạn</li><li>Áp dụng cho sinh viên đại học, cao đẳng</li></ul><p><strong>Sản phẩm áp dụng:</strong> Laptop, máy tính bảng, phụ kiện học tập (bàn phím, chuột, tai nghe...)</p><p><strong>Đăng ký:</strong> Mang thẻ sinh viên đến cửa hàng hoặc gửi ảnh qua chat để xác minh.</p>",
                "sort_order" => 5,
                "is_featured" => 0,
                "faq_catalogue_id" => 7,
                "publish" => 2
            ],
            [
                "question" => "Chương trình giới thiệu bạn bè hoạt động thế nào?",
                "answer" => "<p>Giới thiệu bạn bè mua hàng, cả 2 cùng nhận ưu đãi:</p><p><strong>Cách thức:</strong></p><ol><li>Đăng nhập tài khoản, vào mục \"Giới thiệu bạn bè\"</li><li>Lấy link giới thiệu hoặc mã giới thiệu cá nhân</li><li>Chia sẻ cho bạn bè</li><li>Khi bạn bè mua hàng qua link, cả 2 đều nhận thưởng</li></ol><p><strong>Phần thưởng:</strong></p><ul><li><strong>Người giới thiệu:</strong> 100 điểm (= 10.000đ) hoặc mã giảm 100.000đ cho đơn từ 2 triệu</li><li><strong>Người được giới thiệu:</strong> Mã giảm 100.000đ cho đơn hàng đầu tiên</li></ul><p><strong>Không giới hạn:</strong> Giới thiệu càng nhiều, nhận thưởng càng nhiều. Top giới thiệu nhiều nhất tháng có thưởng đặc biệt.</p>",
                "sort_order" => 6,
                "is_featured" => 0,
                "faq_catalogue_id" => 7,
                "publish" => 2
            ],
            [
                "question" => "Thông tin cá nhân của tôi có được bảo mật không?",
                "answer" => "<p>Chúng tôi cam kết bảo mật tuyệt đối thông tin cá nhân của khách hàng:</p><p><strong>Dữ liệu được mã hóa:</strong></p><ul><li>Sử dụng công nghệ SSL 256-bit để mã hóa dữ liệu</li><li>Thông tin thanh toán được xử lý qua cổng thanh toán an toàn quốc tế</li><li>Mật khẩu được mã hóa không thể đọc được</li></ul><p><strong>Thông tin thu thập:</strong></p><ul><li>Họ tên, email, số điện thoại</li><li>Địa chỉ giao hàng</li><li>Lịch sử mua hàng</li></ul><p><strong>Cam kết:</strong></p><ul><li>Không chia sẻ thông tin cho bên thứ 3 vì mục đích thương mại</li><li>Chỉ sử dụng để xử lý đơn hàng và chăm sóc khách hàng</li><li>Bạn có quyền yêu cầu xóa dữ liệu cá nhân bất cứ lúc nào</li></ul><p>Xem chi tiết tại <strong>Chính sách bảo mật</strong> trên website.</p>",
                "sort_order" => 1,
                "is_featured" => 1,
                "faq_catalogue_id" => 8,
                "publish" => 2
            ],
            [
                "question" => "Thanh toán trực tuyến có an toàn không?",
                "answer" => "<p>Thanh toán trực tuyến trên website hoàn toàn an toàn:</p><p><strong>Các biện pháp bảo mật:</strong></p><ul><li><strong>Chứng chỉ SSL:</strong> Website sử dụng HTTPS mã hóa dữ liệu</li><li><strong>Cổng thanh toán uy tín:</strong> Liên kết với VNPay, OnePay, Momo... đạt chuẩn PCI-DSS</li><li><strong>Xác thực 2 lớp:</strong> OTP qua SMS cho mọi giao dịch</li><li><strong>Không lưu thông tin thẻ:</strong> Thông tin thẻ không được lưu trên hệ thống</li></ul><p><strong>Bảo vệ khách hàng:</strong></p><ul><li>Hoàn tiền 100% nếu giao dịch gian lận</li><li>Giám sát giao dịch bất thường 24/7</li><li>Thông báo ngay khi có giao dịch</li></ul><p><strong>Lưu ý an toàn:</strong></p><ul><li>Không chia sẻ mã OTP với bất kỳ ai</li><li>Kiểm tra URL website (có khóa bảo mật)</li><li>Đăng xuất sau khi sử dụng</li></ul>",
                "sort_order" => 2,
                "is_featured" => 1,
                "faq_catalogue_id" => 8,
                "publish" => 2
            ],
            [
                "question" => "Website có chính sách về cookie không?",
                "answer" => "<p>Có, chúng tôi sử dụng cookie để cải thiện trải nghiệm người dùng:</p><p><strong>Cookie được sử dụng:</strong></p><ul><li><strong>Cookie cần thiết:</strong> Duy trì phiên đăng nhập, giỏ hàng</li><li><strong>Cookie phân tích:</strong> Hiểu hành vi người dùng, cải thiện website</li><li><strong>Cookie quảng cáo:</strong> Hiển thị quảng cáo phù hợp (có thể từ chối)</li></ul><p><strong>Quản lý cookie:</strong></p><ul><li>Bạn có thể chấp nhận hoặc từ chối cookie khi truy cập lần đầu</li><li>Thay đổi cài đặt bất cứ lúc nào trong phần \"Cài đặt Cookie\"</li><li>Xóa cookie qua trình duyệt</li></ul><p><strong>Lưu ý:</strong> Từ chối cookie có thể ảnh hưởng một số tính năng của website (giỏ hàng, đăng nhập tự động...).</p>",
                "sort_order" => 3,
                "is_featured" => 0,
                "faq_catalogue_id" => 8,
                "publish" => 2
            ],
            [
                "question" => "Tôi có thể yêu cầu xóa dữ liệu cá nhân không?",
                "answer" => "<p>Có, theo quy định về bảo vệ dữ liệu, bạn có quyền yêu cầu xóa dữ liệu:</p><p><strong>Quyền của bạn:</strong></p><ul><li>Yêu cầu xem dữ liệu cá nhân được lưu trữ</li><li>Yêu cầu sửa đổi thông tin không chính xác</li><li>Yêu cầu xóa dữ liệu cá nhân</li></ul><p><strong>Cách thực hiện:</strong></p><ol><li>Gửi email đến: privacy@example.com</li><li>Hoặc liên hệ hotline: 1900.xxxx</li><li>Cung cấp thông tin xác nhận danh tính</li><li>Yêu cầu sẽ được xử lý trong 7-14 ngày</li></ol><p><strong>Lưu ý:</strong> Một số dữ liệu cần lưu theo quy định pháp luật (hóa đơn, bảo hành...)</p>",
                "sort_order" => 4,
                "is_featured" => 0,
                "faq_catalogue_id" => 8,
                "publish" => 2
            ],
            [
                "question" => "Điều khoản sử dụng website là gì?",
                "answer" => "<p>Điều khoản sử dụng quy định quyền và trách nhiệm khi sử dụng website:</p><p><strong>Quy định chung:</strong></p><ul><li>Người dùng phải từ 18 tuổi trở lên hoặc có sự đồng ý của phụ huynh</li><li>Thông tin cung cấp phải chính xác và trung thực</li><li>Không sử dụng website cho mục đích bất hợp pháp</li><li>Không spam, hack, phá hoại hệ thống</li></ul><p><strong>Trách nhiệm người dùng:</strong></p><ul><li>Bảo mật thông tin tài khoản</li><li>Chịu trách nhiệm về mọi hoạt động từ tài khoản</li><li>Tuân thủ các quy định về đánh giá, bình luận</li></ul><p><strong>Trách nhiệm website:</strong></p><ul><li>Cung cấp thông tin sản phẩm chính xác</li><li>Bảo mật thông tin khách hàng</li><li>Xử lý khiếu nại một cách công bằng</li></ul><p>Xem đầy đủ tại trang <strong>Điều khoản sử dụng</strong>.</p>",
                "sort_order" => 5,
                "is_featured" => 0,
                "faq_catalogue_id" => 8,
                "publish" => 2
            ],
            [
                "question" => "Làm thế nào để tìm kiếm sản phẩm trên website?",
                "answer" => "<p>Website cung cấp nhiều cách để bạn tìm kiếm sản phẩm nhanh chóng:</p><p><strong>1. Thanh tìm kiếm:</strong></p><ul><li>Nhập tên sản phẩm, hãng, mã sản phẩm vào ô tìm kiếm ở đầu trang</li><li>Hệ thống gợi ý tự động khi bạn gõ</li><li>Nhấn Enter hoặc biểu tượng kính lúp để tìm</li></ul><p><strong>2. Duyệt theo danh mục:</strong></p><ul><li>Nhấn vào menu danh mục sản phẩm</li><li>Chọn danh mục lớn, sau đó thu hẹp theo danh mục con</li></ul><p><strong>3. Lọc và sắp xếp:</strong></p><ul><li><strong>Lọc theo:</strong> Giá, hãng, tính năng, đánh giá...</li><li><strong>Sắp xếp theo:</strong> Giá tăng/giảm, mới nhất, bán chạy, đánh giá cao</li></ul><p><strong>4. Tìm kiếm nâng cao:</strong></p><ul><li>Sử dụng bộ lọc chi tiết (RAM, ROM, màn hình...)</li><li>So sánh nhiều sản phẩm cùng lúc</li></ul><p><strong>Mẹo:</strong> Sử dụng từ khóa ngắn gọn để có kết quả chính xác hơn.</p>",
                "sort_order" => 1,
                "is_featured" => 1,
                "faq_catalogue_id" => 9,
                "publish" => 2
            ],
            [
                "question" => "Giỏ hàng hoạt động như thế nào?",
                "answer" => "<p>Giỏ hàng giúp bạn lưu trữ và quản lý sản phẩm muốn mua:</p><p><strong>Thêm vào giỏ hàng:</strong></p><ul><li>Nhấn nút \"Thêm vào giỏ hàng\" ở trang sản phẩm</li><li>Sản phẩm sẽ được lưu vào giỏ hàng</li><li>Tiếp tục mua sắm hoặc thanh toán ngay</li></ul><p><strong>Quản lý giỏ hàng:</strong></p><ul><li><strong>Xem giỏ hàng:</strong> Nhấn vào biểu tượng giỏ hàng ở góc trên</li><li><strong>Thay đổi số lượng:</strong> Tăng/giảm số lượng sản phẩm</li><li><strong>Xóa sản phẩm:</strong> Nhấn nút X bên cạnh sản phẩm</li><li><strong>Lưu để mua sau:</strong> Chuyển sang danh sách yêu thích</li></ul><p><strong>Lưu ý:</strong></p><ul><li>Giỏ hàng được lưu trong 7 ngày (đã đăng nhập)</li><li>Giỏ hàng khách: Xóa khi đóng trình duyệt</li><li>Giá và tồn kho có thể thay đổi khi thanh toán</li></ul>",
                "sort_order" => 2,
                "is_featured" => 0,
                "faq_catalogue_id" => 9,
                "publish" => 2
            ],
            [
                "question" => "Danh sách yêu thích dùng để làm gì?",
                "answer" => "<p>Danh sách yêu thích giúp bạn lưu lại sản phẩm quan tâm để mua sau:</p><p><strong>Cách sử dụng:</strong></p><ol><li>Nhấn vào biểu tượng trái tim ở sản phẩm</li><li>Sản phẩm được lưu vào danh sách yêu thích</li><li>Xem lại bất cứ lúc nào trong mục \"Yêu thích\"</li></ol><p><strong>Lợi ích:</strong></p><ul><li>So sánh các sản phẩm đã lưu</li><li>Theo dõi thay đổi giá</li><li>Nhận thông báo khi giảm giá hoặc có khuyến mãi</li><li>Chuyển nhanh vào giỏ hàng khi quyết định mua</li></ul><p><strong>Quản lý:</strong></p><ul><li>Xóa sản phẩm không còn quan tâm</li><li>Thêm ghi chú cho từng sản phẩm</li><li>Chia sẻ danh sách với bạn bè</li></ul><p><strong>Lưu ý:</strong> Cần đăng nhập để sử dụng tính năng này.</p>",
                "sort_order" => 3,
                "is_featured" => 0,
                "faq_catalogue_id" => 9,
                "publish" => 2
            ],
            [
                "question" => "Làm thế nào để viết đánh giá sản phẩm?",
                "answer" => "<p>Đánh giá sản phẩm giúp người khác có thêm thông tin và bạn nhận điểm thưởng:</p><p><strong>Điều kiện đánh giá:</strong></p><ul><li>Đã mua và nhận sản phẩm</li><li>Đã đăng nhập tài khoản</li></ul><p><strong>Cách viết đánh giá:</strong></p><ol><li>Vào trang sản phẩm đã mua</li><li>Hoặc vào \"Đơn hàng của tôi\" và chọn \"Đánh giá\"</li><li>Chọn số sao (1-5 sao)</li><li>Viết nhận xét (tối thiểu 50 ký tự)</li><li>Thêm ảnh/video sản phẩm (tùy chọn)</li><li>Nhấn \"Gửi đánh giá\"</li></ol><p><strong>Phần thưởng:</strong></p><ul><li>Đánh giá có ảnh: 20 điểm</li><li>Đánh giá có video: 50 điểm</li><li>Đánh giá chi tiết (>200 ký tự): 30 điểm</li></ul><p><strong>Lưu ý:</strong> Đánh giá cần trung thực, không spam, không chứa ngôn từ xúc phạm.</p>",
                "sort_order" => 4,
                "is_featured" => 0,
                "faq_catalogue_id" => 9,
                "publish" => 2
            ],
            [
                "question" => "Website có ứng dụng di động không?",
                "answer" => "<p>Có, chúng tôi có ứng dụng di động cho cả iOS và Android:</p><p><strong>Tải ứng dụng:</strong></p><ul><li><strong>iOS:</strong> Tìm kiếm trên App Store</li><li><strong>Android:</strong> Tìm kiếm trên Google Play Store</li><li>Hoặc quét mã QR trên website</li></ul><p><strong>Tính năng nổi bật:</strong></p><ul><li>Giao diện tối ưu cho điện thoại</li><li>Thanh toán nhanh hơn</li><li>Nhận thông báo khuyến mãi, flash sale</li><li>Tích hợp quét mã vạch để tra cứu sản phẩm</li><li>Theo dõi đơn hàng realtime</li><li>Sử dụng offline để xem danh sách yêu thích</li></ul><p><strong>Ưu đãi:</strong></p><ul><li>Giảm 50.000đ cho đơn đầu tiên trên app</li><li>Tích điểm nhanh hơn 20%</li><li>Flash sale độc quyền trên app</li></ul>",
                "sort_order" => 5,
                "is_featured" => 0,
                "faq_catalogue_id" => 9,
                "publish" => 2
            ],
            [
                "question" => "Làm sao để liên hệ với bộ phận hỗ trợ khách hàng?",
                "answer" => "<p>Có nhiều cách để bạn liên hệ với chúng tôi:</p><p><strong>1. Hotline:</strong></p><ul><li>Số điện thoại: 1900.xxxx</li><li>Thời gian: 7h30 - 22h00 hàng ngày</li><li>Miễn phí cuộc gọi</li></ul><p><strong>2. Chat trực tuyến:</strong></p><ul><li>Nhấn vào biểu tượng chat ở góc dưới phải website</li><li>Thời gian: 24/7 (bot tự động + nhân viên)</li></ul><p><strong>3. Email:</strong></p><ul><li>Email chung: support@example.com</li><li>Email khiếu nại: complaint@example.com</li><li>Email doanh nghiệp: b2b@example.com</li></ul><p><strong>4. Mạng xã hội:</strong></p><ul><li>Facebook Messenger: facebook.com/tenpage</li><li>Zalo Official Account: zalo.me/tenpage</li></ul><p><strong>5. Tại cửa hàng:</strong></p><ul><li>Hơn 100 chi nhánh toàn quốc</li><li>Xem địa chỉ tại trang \"Hệ thống cửa hàng\"</li></ul>",
                "sort_order" => 1,
                "is_featured" => 1,
                "faq_catalogue_id" => 10,
                "publish" => 2
            ],
            [
                "question" => "Giờ làm việc của cửa hàng là mấy giờ?",
                "answer" => "<p>Hệ thống cửa hàng hoạt động với giờ làm việc linh hoạt:</p><p><strong>Cửa hàng:</strong></p><ul><li><strong>Thứ 2 - Chủ nhật:</strong> 8h00 - 22h00</li><li><strong>Ngày lễ, Tết:</strong> 8h00 - 20h00</li></ul><p><strong>Trung tâm bảo hành:</strong></p><ul><li><strong>Thứ 2 - Thứ 7:</strong> 8h00 - 18h00</li><li><strong>Chủ nhật:</strong> 8h00 - 17h00</li></ul><p><strong>Hỗ trợ trực tuyến:</strong></p><ul><li><strong>Hotline:</strong> 7h30 - 22h00 hàng ngày</li><li><strong>Chat online:</strong> 24/7 (bot tự động sau 22h)</li><li><strong>Email:</strong> Phản hồi trong 24h</li></ul><p><strong>Lưu ý:</strong> Một số chi nhánh có giờ làm việc khác nhau, vui lòng kiểm tra trước khi đến.</p>",
                "sort_order" => 2,
                "is_featured" => 1,
                "faq_catalogue_id" => 10,
                "publish" => 2
            ],
            [
                "question" => "Hệ thống cửa hàng có ở những đâu?",
                "answer" => "<p>Chúng tôi có hệ thống cửa hàng rộng khắp cả nước:</p><p><strong>Hà Nội:</strong></p><ul><li>Hơn 15 chi nhánh tại các quận trung tâm</li><li>Các địa điểm: Cầu Giấy, Đống Đa, Hai Bà Trưng, Hoàn Kiếm, Long Biên...</li></ul><p><strong>TP. Hồ Chí Minh:</strong></p><ul><li>Hơn 20 chi nhánh</li><li>Các địa điểm: Quận 1, 3, 5, 10, Tân Bình, Bình Thạnh, Thủ Đức...</li></ul><p><strong>Các tỉnh thành khác:</strong></p><ul><li>Đà Nẵng, Hải Phòng, Cần Thơ: 3-5 chi nhánh/thành phố</li><li>Các tỉnh: Nghệ An, Thanh Hóa, Huế, Nha Trang, Vũng Tàu, Đồng Nai, Bình Dương...</li></ul><p><strong>Tổng cộng:</strong> Hơn 100 chi nhánh trên toàn quốc.</p><p>Xem danh sách đầy đủ và bản đồ chi tiết tại trang <strong>\"Hệ thống cửa hàng\"</strong> trên website.</p>",
                "sort_order" => 3,
                "is_featured" => 0,
                "faq_catalogue_id" => 10,
                "publish" => 2
            ],
            [
                "question" => "Tôi có thể gửi khiếu nại hoặc phản hồi ở đâu?",
                "answer" => "<p>Chúng tôi luôn lắng nghe và giải quyết mọi khiếu nại của khách hàng:</p><p><strong>Kênh tiếp nhận khiếu nại:</strong></p><ul><li><strong>Hotline khiếu nại:</strong> 1900.xxxx (nhấn phím 4)</li><li><strong>Email:</strong> complaint@example.com</li><li><strong>Form trực tuyến:</strong> Điền form \"Góp ý & Khiếu nại\" trên website</li><li><strong>Trực tiếp tại cửa hàng:</strong> Gặp quản lý cửa hàng</li></ul><p><strong>Quy trình xử lý:</strong></p><ol><li>Tiếp nhận và ghi nhận khiếu nại (trong 2h)</li><li>Phân tích và điều tra (1-3 ngày)</li><li>Đưa ra phương án giải quyết</li><li>Thông báo kết quả cho khách hàng</li><li>Theo dõi và đánh giá mức độ hài lòng</li></ol><p><strong>Cam kết:</strong> Mọi khiếu nại được xử lý công bằng, minh bạch trong vòng 7 ngày làm việc.</p>",
                "sort_order" => 4,
                "is_featured" => 0,
                "faq_catalogue_id" => 10,
                "publish" => 2
            ],
            [
                "question" => "Tôi có thể đặt lịch hẹn trước khi đến cửa hàng không?",
                "answer" => "<p>Có, bạn có thể đặt lịch hẹn để được phục vụ tốt hơn:</p><p><strong>Các dịch vụ có thể đặt lịch:</strong></p><ul><li>Tư vấn mua sắm chi tiết</li><li>Trải nghiệm sản phẩm riêng tư</li><li>Bảo hành và sửa chữa</li><li>Mua hàng số lượng lớn</li><li>Thu cũ đổi mới</li></ul><p><strong>Cách đặt lịch:</strong></p><ol><li><strong>Online:</strong> Truy cập trang \"Đặt lịch hẹn\" trên website</li><li><strong>Hotline:</strong> Gọi 1900.xxxx để đặt lịch</li><li><strong>App di động:</strong> Đặt lịch trực tiếp trên ứng dụng</li></ol><p><strong>Thông tin cần cung cấp:</strong> Họ tên, SĐT, dịch vụ cần sử dụng, thời gian mong muốn, chi nhánh.</p><p><strong>Lợi ích:</strong> Không phải chờ đợi, được ưu tiên phục vụ, tư vấn viên chuyên biệt.</p>",
                "sort_order" => 5,
                "is_featured" => 0,
                "faq_catalogue_id" => 10,
                "publish" => 2
            ],
            [
                "question" => "Cửa hàng có dịch vụ giao hàng tận nơi và lắp đặt không?",
                "answer" => "<p>Có, chúng tôi cung cấp dịch vụ giao hàng và lắp đặt chuyên nghiệp:</p><p><strong>Giao hàng tận nơi:</strong></p><ul><li>Toàn quốc với nhiều hình thức vận chuyển</li><li>Giao hàng nhanh trong 2-4h (nội thành HN, HCM)</li><li>Miễn phí ship cho đơn từ 500.000đ</li><li>Shipper tự giao hoặc qua đối tác vận chuyển uy tín</li></ul><p><strong>Dịch vụ lắp đặt:</strong></p><ul><li><strong>Miễn phí:</strong> Smart TV, máy giặt, tủ lạnh, máy lạnh (kiểm tra và khởi động máy)</li><li><strong>Có phí:</strong> Lắp đặt phức tạp (đục tường, kéo dài đường ống...)</li><li><strong>Hướng dẫn sử dụng:</strong> Kỹ thuật viên hướng dẫn chi tiết cách sử dụng</li></ul><p><strong>Đặt lịch lắp đặt:</strong> Thông báo trước 1 ngày để sắp xếp kỹ thuật viên.</p>",
                "sort_order" => 6,
                "is_featured" => 0,
                "faq_catalogue_id" => 10,
                "publish" => 2
            ],
            [
                "question" => "Tôi có thể yêu cầu hỗ trợ từ xa không?",
                "answer" => "<p>Có, chúng tôi cung cấp dịch vụ hỗ trợ từ xa miễn phí:</p><p><strong>Hỗ trợ qua điện thoại:</strong></p><ul><li>Hotline: 1900.xxxx (7h30 - 22h)</li><li>Hướng dẫn cài đặt, sử dụng</li><li>Xử lý sự cố cơ bản</li><li>Tư vấn kỹ thuật</li></ul><p><strong>Hỗ trợ qua TeamViewer/AnyDesk:</strong></p><ul><li>Áp dụng cho laptop, PC</li><li>Kỹ thuật viên điều khiển từ xa để xử lý</li><li>Cài đặt phần mềm, driver</li><li>Khắc phục lỗi hệ thống</li></ul><p><strong>Hỗ trợ qua video call:</strong></p><ul><li>Zalo, Messenger video call</li><li>Hướng dẫn trực quan bằng hình ảnh</li><li>Kiểm tra tình trạng sản phẩm</li></ul><p><strong>Lưu ý:</strong> Dịch vụ hoàn toàn miễn phí. Nếu cần hỗ trợ tại nhà, có thể mất phí dịch vụ tùy khu vực.</p>",
                "sort_order" => 7,
                "is_featured" => 0,
                "faq_catalogue_id" => 10,
                "publish" => 2
            ]
        ];

        foreach ($data as $item) {
            Faq::create([
                'question' => $item['question'],
                'slug' => Str::slug($item['question']),
                'answer' => $item['answer'],
                'order' => $item['sort_order'],
                'is_hot' => (bool)$item['is_featured'],
                'faq_catalogue_id' => $item['faq_catalogue_id'],
                'publish' => $item['publish'],
                'user_id' => 1,
            ]);
        }
    }
}
