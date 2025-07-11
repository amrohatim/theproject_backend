<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <aside class="hidden lg:flex lg:flex-shrink-0">
      <div class="flex flex-col w-64 bg-white border-r border-gray-200">
        <!-- Logo -->
        <div class="flex items-center justify-center h-16 px-4 border-b border-gray-200">
          <div class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
              <span class="text-white font-bold text-sm">D3</span>
            </div>
            <span class="text-xl font-bold text-gray-900">Data3Chic</span>
          </div>
        </div>
        
        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
          <a href="#" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-50">
            <LayoutDashboardIcon class="w-5 h-5 mr-3" />
            Dashboard
          </a>
          
          <a href="#" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-50">
            <PackageIcon class="w-5 h-5 mr-3" />
            Products
          </a>
          
          <a href="#" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-50">
            <SettingsIcon class="w-5 h-5 mr-3" />
            Services
          </a>
          
          <a href="#" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-50">
            <ClipboardListIcon class="w-5 h-5 mr-3" />
            Orders
          </a>
          
          <a href="#" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-50">
            <UsersIcon class="w-5 h-5 mr-3" />
            Customers
          </a>
          
          <a href="#" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-50">
            <BarChart3Icon class="w-5 h-5 mr-3" />
            Reports
          </a>
          
          <!-- Settings Section -->
          <div class="pt-6">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Settings</p>
            <div class="mt-2 space-y-1">
              <a href="#" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg bg-blue-50 text-blue-700 border-r-2 border-blue-600">
                <UserIcon class="w-5 h-5 mr-3" />
                Personal Settings
              </a>
              
              <a href="#" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-50">
                <SettingsIcon class="w-5 h-5 mr-3" />
                Global Settings
              </a>
            </div>
          </div>
        </nav>
        
        <!-- Logout -->
        <div class="p-4 border-t border-gray-200">
          <button class="flex items-center w-full px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50">
            <LogOutIcon class="w-5 h-5 mr-3" />
            Logout
          </button>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Top Navigation -->
      <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="flex items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
          <!-- Mobile menu button -->
          <button type="button" class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
            <MenuIcon class="h-6 w-6" />
          </button>
          
          <!-- Page title -->
          <div class="flex-1 lg:flex-none">
            <h1 class="text-2xl font-bold text-gray-900">Personal Settings</h1>
          </div>
          
          <!-- Right side -->
          <div class="flex items-center space-x-4">
            <!-- Notifications -->
            <button type="button" class="p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-full">
              <BellIcon class="h-6 w-6" />
            </button>
            
            <!-- Profile dropdown -->
            <div class="relative" @click.away="profileDropdown = false">
              <button @click="profileDropdown = !profileDropdown" type="button" class="flex items-center space-x-3 text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                <img class="h-8 w-8 rounded-full object-cover" src="https://ui-avatars.com/api/?name=Test+Merchant&color=7F9CF5&background=EBF4FF" alt="Profile">
                <span class="hidden lg:block text-sm font-medium text-gray-700">Test Merchant</span>
                <ChevronDownIcon class="hidden lg:block h-4 w-4 text-gray-400" />
              </button>
              
              <div v-show="profileDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 z-50">
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Your Profile</a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign out</button>
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <!-- Success Message -->
          <div v-if="successMessage" class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
              <CheckCircleIcon class="h-5 w-5 text-green-400" />
              <p class="ml-3 text-sm font-medium text-green-800">{{ successMessage }}</p>
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
              <!-- Personal Information Card -->
              <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200">
                  <div class="flex items-center">
                    <div class="flex-shrink-0">
                      <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <UserIcon class="w-6 h-6 text-blue-600" />
                      </div>
                    </div>
                    <div class="ml-4">
                      <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                      <p class="text-sm text-gray-500">Update your personal details and account settings</p>
                    </div>
                  </div>
                </div>
                
                <form @submit.prevent="updatePersonalInfo" class="p-6">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Full Name -->
                    <div class="md:col-span-2">
                      <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Full Name <span class="text-red-500">*</span>
                      </label>
                      <input 
                        type="text" 
                        id="full_name" 
                        v-model="personalInfo.fullName"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        :class="{ 'border-red-300': errors.fullName }"
                        placeholder="Enter your full name">
                      <p v-if="errors.fullName" class="mt-1 text-sm text-red-600">{{ errors.fullName }}</p>
                    </div>
                    
                    <!-- Email Address -->
                    <div>
                      <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address <span class="text-red-500">*</span>
                      </label>
                      <input 
                        type="email" 
                        id="email" 
                        v-model="personalInfo.email"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        :class="{ 'border-red-300': errors.email }"
                        placeholder="Enter your email address">
                      <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
                    </div>
                    
                    <!-- Phone Number -->
                    <div>
                      <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Phone Number
                      </label>
                      <input 
                        type="tel" 
                        id="phone" 
                        v-model="personalInfo.phone"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        placeholder="Enter your phone number">
                    </div>
                  </div>
                  
                  <div class="mt-8 flex justify-end">
                    <button 
                      type="submit" 
                      :disabled="isLoading"
                      class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50">
                      <span v-if="isLoading">Saving...</span>
                      <span v-else>Save Changes</span>
                    </button>
                  </div>
                </form>
              </div>
              
              <!-- Change Password Card -->
              <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200">
                  <div class="flex items-center">
                    <div class="flex-shrink-0">
                      <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                        <LockIcon class="w-6 h-6 text-amber-600" />
                      </div>
                    </div>
                    <div class="ml-4">
                      <h3 class="text-lg font-semibold text-gray-900">Change Password</h3>
                      <p class="text-sm text-gray-500">Update your account password for security</p>
                    </div>
                  </div>
                </div>
                
                <form @submit.prevent="updatePassword" class="p-6">
                  <div class="space-y-6">
                    <!-- Current Password -->
                    <div>
                      <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                        Current Password <span class="text-red-500">*</span>
                      </label>
                      <input 
                        type="password" 
                        id="current_password" 
                        v-model="passwordForm.currentPassword"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        :class="{ 'border-red-300': errors.currentPassword }"
                        placeholder="Enter your current password">
                      <p v-if="errors.currentPassword" class="mt-1 text-sm text-red-600">{{ errors.currentPassword }}</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <!-- New Password -->
                      <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                          New Password <span class="text-red-500">*</span>
                        </label>
                        <input 
                          type="password" 
                          id="new_password" 
                          v-model="passwordForm.newPassword"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                          :class="{ 'border-red-300': errors.newPassword }"
                          placeholder="Enter new password">
                        <p v-if="errors.newPassword" class="mt-1 text-sm text-red-600">{{ errors.newPassword }}</p>
                      </div>
                      
                      <!-- Confirm New Password -->
                      <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                          Confirm New Password <span class="text-red-500">*</span>
                        </label>
                        <input 
                          type="password" 
                          id="confirm_password" 
                          v-model="passwordForm.confirmPassword"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                          :class="{ 'border-red-300': errors.confirmPassword }"
                          placeholder="Confirm new password">
                        <p v-if="errors.confirmPassword" class="mt-1 text-sm text-red-600">{{ errors.confirmPassword }}</p>
                      </div>
                    </div>
                    
                    <!-- Password Requirements -->
                    <div class="bg-gray-50 rounded-lg p-4">
                      <h4 class="text-sm font-medium text-gray-900 mb-2">Password Requirements:</h4>
                      <ul class="text-sm text-gray-600 space-y-1">
                        <li class="flex items-center">
                          <CheckIcon class="w-4 h-4 text-gray-400 mr-2" />
                          At least 8 characters long
                        </li>
                        <li class="flex items-center">
                          <CheckIcon class="w-4 h-4 text-gray-400 mr-2" />
                          Contains uppercase and lowercase letters
                        </li>
                        <li class="flex items-center">
                          <CheckIcon class="w-4 h-4 text-gray-400 mr-2" />
                          Contains at least one number
                        </li>
                      </ul>
                    </div>
                  </div>
                  
                  <div class="mt-8 flex justify-end">
                    <button 
                      type="submit" 
                      :disabled="isLoading"
                      class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50">
                      <span v-if="isLoading">Updating...</span>
                      <span v-else>Update Password</span>
                    </button>
                  </div>
                </form>
              </div>
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-6">
              <!-- Account Information Card -->
              <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200">
                  <div class="flex items-center">
                    <div class="flex-shrink-0">
                      <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <CheckCircleIcon class="w-6 h-6 text-green-600" />
                      </div>
                    </div>
                    <div class="ml-4">
                      <h3 class="text-lg font-semibold text-gray-900">Account Information</h3>
                      <p class="text-sm text-gray-500">Your account details and verification status</p>
                    </div>
                  </div>
                </div>
                
                <div class="p-6 space-y-4">
                  <!-- Account Type -->
                  <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <div>
                      <p class="text-sm font-medium text-gray-900">Account Type</p>
                      <p class="text-sm text-gray-500">Merchant</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      Merchant
                    </span>
                  </div>
                  
                  <!-- Member Since -->
                  <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <div>
                      <p class="text-sm font-medium text-gray-900">Member Since</p>
                      <p class="text-sm text-gray-500">Jun 30, 2025</p>
                    </div>
                    <p class="text-sm text-gray-600">Jun 30, 2025</p>
                  </div>
                  
                  <!-- Email Status -->
                  <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <div>
                      <p class="text-sm font-medium text-gray-900">Email Status</p>
                      <p class="text-sm text-gray-500">Last Updated: Jul 01, 2025</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      <CheckIcon class="w-3 h-3 mr-1" />
                      Verified
                    </span>
                  </div>
                  
                  <!-- Phone Status -->
                  <div class="flex items-center justify-between py-3">
                    <div>
                      <p class="text-sm font-medium text-gray-900">Phone Status</p>
                      <p class="text-sm text-gray-500">Merchant Status</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      <CheckIcon class="w-3 h-3 mr-1" />
                      Verified
                    </span>
                  </div>
                </div>
              </div>
              
              <!-- Quick Actions Card -->
              <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200">
                  <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                </div>
                
                <div class="p-6 space-y-3">
                  <a href="#" class="flex items-center p-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <SettingsIcon class="w-5 h-5 mr-3 text-gray-400" />
                    Global Settings
                  </a>
                  
                  <a href="#" class="flex items-center p-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <HelpCircleIcon class="w-5 h-5 mr-3 text-gray-400" />
                    Help & Support
                  </a>
                  
                  <a href="#" class="flex items-center p-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <ShieldIcon class="w-5 h-5 mr-3 text-gray-400" />
                    Privacy Policy
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { 
  UserIcon, 
  LockIcon, 
  CheckCircleIcon, 
  CheckIcon, 
  SettingsIcon, 
  HelpCircleIcon, 
  ShieldIcon,
  LayoutDashboardIcon,
  PackageIcon,
  ClipboardListIcon,
  UsersIcon,
  BarChart3Icon,
  LogOutIcon,
  MenuIcon,
  BellIcon,
  ChevronDownIcon
} from 'lucide-vue-next'

// Reactive data
const profileDropdown = ref(false)
const isLoading = ref(false)
const successMessage = ref('')

const personalInfo = reactive({
  fullName: 'Test Merchant Name',
  email: 'merchant@test.com',
  phone: '+971501234567'
})

const passwordForm = reactive({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
})

const errors = reactive({})

// Methods
const updatePersonalInfo = async () => {
  // Clear previous errors
  Object.keys(errors).forEach(key => delete errors[key])
  
  // Validation
  if (!personalInfo.fullName.trim()) {
    errors.fullName = 'Full name is required'
  }
  
  if (!personalInfo.email.trim()) {
    errors.email = 'Email is required'
  } else if (!/\S+@\S+\.\S+/.test(personalInfo.email)) {
    errors.email = 'Please enter a valid email address'
  }
  
  if (Object.keys(errors).length > 0) {
    return
  }
  
  isLoading.value = true
  
  // Simulate API call
  setTimeout(() => {
    isLoading.value = false
    successMessage.value = 'Personal information updated successfully!'
    
    // Clear success message after 5 seconds
    setTimeout(() => {
      successMessage.value = ''
    }, 5000)
  }, 1000)
}

const updatePassword = async () => {
  // Clear previous errors
  Object.keys(errors).forEach(key => {
    if (key.includes('Password')) {
      delete errors[key]
    }
  })
  
  // Validation
  if (!passwordForm.currentPassword) {
    errors.currentPassword = 'Current password is required'
  }
  
  if (!passwordForm.newPassword) {
    errors.newPassword = 'New password is required'
  } else if (passwordForm.newPassword.length < 8) {
    errors.newPassword = 'Password must be at least 8 characters long'
  }
  
  if (!passwordForm.confirmPassword) {
    errors.confirmPassword = 'Please confirm your new password'
  } else if (passwordForm.newPassword !== passwordForm.confirmPassword) {
    errors.confirmPassword = 'Passwords do not match'
  }
  
  if (Object.keys(errors).some(key => key.includes('Password'))) {
    return
  }
  
  isLoading.value = true
  
  // Simulate API call
  setTimeout(() => {
    isLoading.value = false
    successMessage.value = 'Password updated successfully!'
    
    // Clear form
    passwordForm.currentPassword = ''
    passwordForm.newPassword = ''
    passwordForm.confirmPassword = ''
    
    // Clear success message after 5 seconds
    setTimeout(() => {
      successMessage.value = ''
    }, 5000)
  }, 1000)
}
</script>

<style scoped>
/* Custom scrollbar for webkit browsers */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

/* Smooth transitions */
* {
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}
</style>