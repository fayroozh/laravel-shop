import { create } from 'zustand'
import { persist } from 'zustand/middleware'

const useAuthStore = create(
  persist(
    (set) => ({
      token: null,
      user: null,
      isAuthenticated: false,
      
      // Set token and auth status
      setToken: (token) => set({ 
        token,
        isAuthenticated: !!token 
      }),
      
      // Set user data
      setUser: (user) => set({
        user
      }),

      // Clear token and auth status
      logout: () => set({ 
        token: null,
        user: null,
        isAuthenticated: false
      }),
    }),
    {
      name: 'auth-storage', 
      getStorage: () => localStorage,
    }
  )
)

export default useAuthStore
