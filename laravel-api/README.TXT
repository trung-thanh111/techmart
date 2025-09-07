`Luồng module căn bản theo thứ tự`
1. Route (định nghĩa tuyến đường)
2. Request (validation)
3. Middleware (Phân quyền, )
4. Controller Layer
   4.1 BaseController xử lý request chung ->tương tác với service
5. Service Layer (cả Interfaces)
   5.1 BaseService (logic chính của dự án -> tương tác với Repository)
6. Repository Layer (kế thừa lại và override lại or triển khai riêng layer)
   6.1 BaseRepository (các phương thức dùng chung -> tương tác với model)
7. Model (migration, seeder, quan hệ, fillable)
8. Resource (tùy chỉnh đầu ra api)
9. Provider (đăng ký các Service sử dụng)

Ngoài ra còn dùng (Classes, Console, Enum, Exception, Helper, Pipeline, Trait())

upload ảnh
phải upload ảnh tạm để review trước (upload-temp) -> lấy path được trả về để lưu vào từng module -> dùng cho album nhiều ảnh
còn 1 ảnh có thể dùng upload image service đơn như userservices
