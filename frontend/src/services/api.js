import axios from 'axios'

const api = axios.create({
  baseURL: '/api',
  headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
})

// Attach token from localStorage on every request
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('bssahty_token')
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})

// Global response error handling
api.interceptors.response.use(
  (res) => res,
  (error) => {
    if (error.response?.status === 401) {
      const isAuthPage = window.location.pathname === '/login' || window.location.pathname === '/register'
      if (!isAuthPage) {
        localStorage.removeItem('bssahty_token')
        const currentPath = window.location.pathname + window.location.search
        window.location.href = `/login?redirect=${encodeURIComponent(currentPath)}`
      }
    }
    return Promise.reject(error)
  }
)

// ─── Auth ──────────────────────────────────────────────────────────────────
export const authApi = {
  register: (data)  => api.post('/register', data),
  login:    (data)  => api.post('/login', data),
  logout:   ()      => api.post('/logout'),
  me:       ()      => api.get('/me'),
}

// ─── Events ────────────────────────────────────────────────────────────────
export const eventsApi = {
  list:   (params)  => api.get('/events', { params }),
  create: (data)    => api.post('/events', data),
  get:    (id)      => api.get(`/events/${id}`),
  update: (id, data)=> api.put(`/events/${id}`, data),
  delete: (id)      => api.delete(`/events/${id}`),
}

// ─── Community (Feed / Questions) ──────────────────────────────────────────
export const feedApi = {
  list:    (params) => api.get('/questions', { params }),
  create:  (data)   => api.post('/questions', data),
  react:   (data) => api.post('/reactions', {
    type: data.type || 'LIKE',
    reactable_id: data.question_id,
    reactable_type: 'App\\Models\\Question'
  }),
  comment: (data)   => api.post('/comments', data),
}

// ─── Spots ─────────────────────────────────────────────────────────────────
export const spotsApi = {
  list:   ()            => api.get('/spots'),
  nearby: (lat, lng)    => api.get('/spots/nearby', { params: { latitude: lat, longitude: lng } }),
  create: (data)        => api.post('/spots', data),
}

// ─── Messaging ─────────────────────────────────────────────────────────────
export const messagingApi = {
  conversations:    ()        => api.get('/conversations'),
  store:            (data)    => api.post('/conversations', data),
  conversation:     (id)      => api.get(`/conversations/${id}`),
  send:             (data)    => api.post('/messages', data),
  markRead:         (id)      => api.patch(`/messages/${id}/read`),
  unreadCount:      ()        => api.get('/messages/unread-count'),
}

// ─── Admin ──────────────────────────────────────────────────────────────────
export const adminApi = {
  stats:        ()        => api.get('/admin/stats'),
  pendingSpots: ()        => api.get('/admin/spots/pending'),
  approveSpot:  (id)      => api.patch(`/admin/spots/${id}/approve`),
  rejectSpot:   (id)      => api.patch(`/admin/spots/${id}/reject`),
  banUser:      (id)      => api.patch(`/admin/users/${id}/ban`),
  unbanUser:    (id)      => api.patch(`/admin/users/${id}/unban`),
}

// ─── Users ──────────────────────────────────────────────────────────────────
export const userApi = {
  get:          (id)      => api.get(`/users/${id}`),
  update:       (data)    => api.put('/me', data),
  friendships:  ()        => api.get('/friendships'),
  addFriend:    (id)      => api.post('/friendships', { friend_id: id }),
  removeFriend: (id)      => api.delete(`/friendships/${id}`),
}

export default api
