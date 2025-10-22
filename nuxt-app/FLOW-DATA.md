# Flow data nuxt4

1. interfaces
2. services
3. store (đối với các trường hợp lưu data toàn cục -> get, pagination không cần)
4. compossables (gọi từ service)


Component → useAuth() → authService → useApi() → Backend
    ↓
  Store (Pinia) ← Response ← API Call
    ↓
  State Update → UI Re-render