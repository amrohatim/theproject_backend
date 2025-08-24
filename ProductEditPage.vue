<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-4 md:p-6">
    <div class="max-w-7xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
          <button class="flex items-center gap-2 px-4 py-2 border border-slate-300 rounded-lg bg-white hover:bg-slate-50 transition-colors">
            <ArrowLeftIcon class="w-4 h-4" />
            Back to Products
          </button>
          <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-900">Edit Product</h1>
            <p class="text-slate-600 mt-1">Update product information, colors, and specifications</p>
          </div>
        </div>
        <div class="flex gap-2">
          <button class="px-4 py-2 border border-slate-300 rounded-lg bg-white hover:bg-slate-50 transition-colors">
            Preview
          </button>
          <button class="flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
            <SaveIcon class="w-4 h-4" />
            Save Changes
          </button>
        </div>
      </div>

      <!-- Progress Indicator -->
      <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-emerald-800">Stock Allocation Progress</span>
          <span class="text-sm text-emerald-700">
            {{ totalAllocatedStock }} / {{ product.stock }} units allocated
          </span>
        </div>
        <div class="w-full bg-emerald-200 rounded-full h-2">
          <div 
            class="bg-emerald-600 h-2 rounded-full transition-all duration-300" 
            :style="{ width: `${Math.min(stockProgress, 100)}%` }"
          ></div>
        </div>
        <div v-if="stockProgress > 100" class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
          <div class="flex items-center gap-2">
            <AlertCircleIcon class="w-4 h-4 text-amber-600" />
            <p class="text-amber-800 text-sm">
              You've allocated more stock than available. Please adjust color stock quantities.
            </p>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="space-y-6">
        <!-- Tab Navigation -->
        <div class="flex border-b border-slate-200 bg-white rounded-t-lg">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              'flex items-center gap-2 px-6 py-3 font-medium text-sm transition-colors',
              activeTab === tab.id
                ? 'text-emerald-600 border-b-2 border-emerald-600 bg-emerald-50'
                : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'
            ]"
          >
            <component :is="tab.icon" class="w-4 h-4" />
            <span class="hidden sm:inline">{{ tab.label }}</span>
          </button>
        </div>

        <!-- Basic Information Tab -->
        <div v-show="activeTab === 'basic'" class="space-y-6">
          <div class="grid lg:grid-cols-2 gap-6">
            <!-- Product Details -->
            <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
              <div class="p-6 border-b border-slate-200">
                <h3 class="flex items-center gap-2 text-lg font-semibold text-slate-900">
                  <PackageIcon class="w-5 h-5 text-slate-600" />
                  Product Details
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div class="space-y-2">
                  <label for="name" class="block text-sm font-medium text-slate-700">
                    Product Name <span class="text-red-500">*</span>
                  </label>
                  <input
                    id="name"
                    v-model="product.name"
                    type="text"
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                  />
                </div>

                <div class="grid grid-cols-2 gap-4">
                  <div class="space-y-2">
                    <label for="category" class="block text-sm font-medium text-slate-700">
                      Category <span class="text-red-500">*</span>
                    </label>
                    <select
                      id="category"
                      v-model="product.categoryId"
                      class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                    >
                      <option value="">Select category</option>
                      <option value="clothing">Clothing</option>
                      <option value="accessories">Accessories</option>
                      <option value="shoes">Shoes</option>
                    </select>
                  </div>

                  <div class="space-y-2">
                    <label for="branch" class="block text-sm font-medium text-slate-700">
                      Branch <span class="text-red-500">*</span>
                    </label>
                    <select
                      id="branch"
                      v-model="product.branchId"
                      class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                    >
                      <option value="">Select branch</option>
                      <option value="main">Main Store</option>
                      <option value="downtown">Downtown</option>
                      <option value="mall">Shopping Mall</option>
                    </select>
                  </div>
                </div>

                <div class="space-y-2">
                  <label for="description" class="block text-sm font-medium text-slate-700">
                    Description
                  </label>
                  <textarea
                    id="description"
                    v-model="product.description"
                    rows="4"
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- Pricing & Inventory -->
            <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
              <div class="p-6 border-b border-slate-200">
                <h3 class="flex items-center gap-2 text-lg font-semibold text-slate-900">
                  <DollarSignIcon class="w-5 h-5 text-slate-600" />
                  Pricing & Inventory
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                  <div class="space-y-2">
                    <label for="price" class="block text-sm font-medium text-slate-700">
                      Current Price <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                      <DollarSignIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400" />
                      <input
                        id="price"
                        v-model.number="product.price"
                        type="number"
                        step="0.01"
                        class="w-full pl-10 pr-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                      />
                    </div>
                  </div>

                  <div class="space-y-2">
                    <label for="originalPrice" class="block text-sm font-medium text-slate-700">
                      Original Price
                    </label>
                    <div class="relative">
                      <DollarSignIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400" />
                      <input
                        id="originalPrice"
                        v-model.number="product.originalPrice"
                        type="number"
                        step="0.01"
                        class="w-full pl-10 pr-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                      />
                    </div>
                  </div>
                </div>

                <div class="space-y-2">
                  <label for="stock" class="block text-sm font-medium text-slate-700">
                    Total Stock <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <WarehouseIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400" />
                    <input
                      id="stock"
                      v-model.number="product.stock"
                      type="number"
                      class="w-full pl-10 pr-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                    />
                  </div>
                  <p class="text-xs text-slate-500">Total inventory to be allocated across color variants</p>
                </div>

                <div class="flex items-center space-x-2">
                  <input
                    id="available"
                    v-model="product.isAvailable"
                    type="checkbox"
                    class="w-4 h-4 text-emerald-600 bg-gray-100 border-gray-300 rounded focus:ring-emerald-500"
                  />
                  <label for="available" class="text-sm font-medium text-slate-700">
                    Available for purchase
                  </label>
                </div>

                <div v-if="product.originalPrice > product.price" class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg">
                  <div class="flex items-center gap-2">
                    <span class="px-2 py-1 bg-emerald-100 text-emerald-800 text-xs font-medium rounded">
                      Sale
                    </span>
                    <span class="text-sm text-emerald-700">
                      {{ Math.round(((product.originalPrice - product.price) / product.originalPrice) * 100) }}% off
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Colors & Images Tab -->
        <div v-show="activeTab === 'colors'" class="space-y-6">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-lg font-semibold text-slate-900">Product Colors</h3>
              <p class="text-sm text-slate-600">Add color variants with images and size options</p>
            </div>
            <button
              @click="addColor"
              class="flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors"
            >
              <PlusIcon class="w-4 h-4" />
              Add Color
            </button>
          </div>

          <!-- Empty State -->
          <div v-if="colors.length === 0" class="bg-white border-2 border-dashed border-slate-300 rounded-lg">
            <div class="flex flex-col items-center justify-center py-12">
              <PaletteIcon class="w-12 h-12 text-slate-400 mb-4" />
              <h3 class="text-lg font-medium text-slate-900 mb-2">No colors added</h3>
              <p class="text-slate-600 text-center mb-4">
                Add at least one color variant with an image to continue
              </p>
              <button
                @click="addColor"
                class="flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors"
              >
                <PlusIcon class="w-4 h-4" />
                Add Your First Color
              </button>
            </div>
          </div>

          <!-- Color Cards -->
          <div class="grid gap-6">
            <div
              v-for="(color, index) in colors"
              :key="color.id"
              :class="[
                'bg-white border rounded-lg shadow-sm transition-all duration-200',
                color.isDefault ? 'ring-2 ring-emerald-500 border-emerald-200' : 'border-slate-200'
              ]"
            >
              <div class="p-6 border-b border-slate-200">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <div
                      class="w-6 h-6 rounded-full border-2 border-white shadow-sm"
                      :style="{ backgroundColor: color.colorCode }"
                    ></div>
                    <h4 class="text-lg font-semibold text-slate-900">
                      Color Variant {{ index + 1 }}
                      <span
                        v-if="color.isDefault"
                        class="ml-2 inline-flex items-center px-2 py-1 bg-emerald-100 text-emerald-800 text-xs font-medium rounded"
                      >
                        <StarIcon class="w-3 h-3 mr-1" />
                        Default
                      </span>
                    </h4>
                  </div>
                  <div class="flex items-center gap-2">
                    <button
                      v-if="!color.isDefault"
                      @click="setDefaultColor(color.id)"
                      class="px-3 py-1 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors text-sm"
                    >
                      Set as Default
                    </button>
                    <button
                      @click="removeColor(color.id)"
                      class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                    >
                      <Trash2Icon class="w-4 h-4" />
                    </button>
                  </div>
                </div>
              </div>
              
              <div class="p-6 space-y-6">
                <div class="grid lg:grid-cols-2 gap-6">
                  <!-- Color Details -->
                  <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                      <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700">
                          Color Name <span class="text-red-500">*</span>
                        </label>
                        <select
                          v-model="color.name"
                          class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                        >
                          <option value="">Select color</option>
                          <option value="Red">Red</option>
                          <option value="Blue">Blue</option>
                          <option value="Green">Green</option>
                          <option value="Navy Blue">Navy Blue</option>
                          <option value="Forest Green">Forest Green</option>
                          <option value="Black">Black</option>
                          <option value="White">White</option>
                          <option value="Gray">Gray</option>
                        </select>
                      </div>

                      <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700">Color Code</label>
                        <div class="flex gap-2">
                          <input
                            v-model="color.colorCode"
                            type="color"
                            class="w-12 h-10 p-1 border border-slate-300 rounded-lg"
                          />
                          <input
                            v-model="color.colorCode"
                            type="text"
                            placeholder="#000000"
                            class="flex-1 px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                          />
                        </div>
                      </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                      <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700">Price Adjustment</label>
                        <div class="relative">
                          <DollarSignIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400" />
                          <input
                            v-model.number="color.priceAdjustment"
                            type="number"
                            step="0.01"
                            class="w-full pl-10 pr-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                          />
                        </div>
                      </div>

                      <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700">Stock Allocation</label>
                        <input
                          v-model.number="color.stock"
                          type="number"
                          class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                        />
                      </div>
                    </div>
                  </div>

                  <!-- Image Upload -->
                  <div class="space-y-4">
                    <label class="block text-sm font-medium text-slate-700">
                      Product Image <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                      <div class="aspect-[3/4] bg-slate-50 border-2 border-dashed border-slate-300 rounded-lg overflow-hidden hover:border-emerald-400 transition-colors">
                        <div v-if="color.image" class="relative w-full h-full group">
                          <img
                            :src="color.image"
                            :alt="`${color.name} variant`"
                            class="w-full h-full object-cover"
                          />
                          <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 flex items-center justify-center">
                            <button
                              @click="triggerImageUpload(color.id)"
                              class="opacity-0 group-hover:opacity-100 transition-opacity px-3 py-2 bg-white text-slate-700 rounded-lg shadow-sm hover:bg-slate-50"
                            >
                              <UploadIcon class="w-4 h-4 mr-2 inline" />
                              Change Image
                            </button>
                          </div>
                          <div v-if="color.isDefault" class="absolute top-2 right-2">
                            <span class="inline-flex items-center px-2 py-1 bg-emerald-600 text-white text-xs font-medium rounded">
                              <StarIcon class="w-3 h-3 mr-1" />
                              Main Image
                            </span>
                          </div>
                        </div>
                        <div
                          v-else
                          @click="triggerImageUpload(color.id)"
                          class="w-full h-full flex flex-col items-center justify-center cursor-pointer hover:bg-slate-100 transition-colors"
                        >
                          <ImageIcon class="w-12 h-12 text-slate-400 mb-3" />
                          <p class="text-sm font-medium text-slate-600 mb-1">Upload Image</p>
                          <p class="text-xs text-slate-500">PNG, JPG up to 2MB</p>
                        </div>
                      </div>
                      <input
                        :ref="`fileInput-${color.id}`"
                        type="file"
                        accept="image/*"
                        class="hidden"
                        @change="handleImageUpload(color.id, $event)"
                      />
                    </div>
                  </div>
                </div>

                <!-- Size Variants -->
                <div v-if="color.sizes.length > 0" class="space-y-4">
                  <div class="flex items-center justify-between">
                    <h4 class="font-medium text-slate-900">Size Variants</h4>
                    <span class="px-2 py-1 bg-slate-100 text-slate-700 text-xs rounded">
                      {{ color.sizes.length }} sizes
                    </span>
                  </div>
                  <div class="grid gap-3">
                    <div
                      v-for="size in color.sizes"
                      :key="size.id"
                      class="flex items-center gap-4 p-3 bg-slate-50 rounded-lg"
                    >
                      <div class="flex-1">
                        <div class="font-medium text-sm">{{ size.name }}</div>
                        <div class="text-xs text-slate-500">{{ size.value }}</div>
                      </div>
                      <div class="text-sm">
                        <span class="font-medium">{{ size.stock }}</span> units
                      </div>
                      <div v-if="size.priceAdjustment !== 0" class="text-sm">
                        <span :class="size.priceAdjustment > 0 ? 'text-emerald-600' : 'text-red-600'">
                          {{ size.priceAdjustment > 0 ? '+' : '' }}${{ size.priceAdjustment }}
                        </span>
                      </div>
                      <div class="flex items-center">
                        <CheckIcon v-if="size.isAvailable" class="w-4 h-4 text-emerald-600" />
                        <div v-else class="w-4 h-4 rounded-full bg-slate-300"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Specifications Tab -->
        <div v-show="activeTab === 'specifications'" class="space-y-6">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-lg font-semibold text-slate-900">Product Specifications</h3>
              <p class="text-sm text-slate-600">Add technical details and product features</p>
            </div>
            <button
              @click="addSpecification"
              class="flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors"
            >
              <PlusIcon class="w-4 h-4" />
              Add Specification
            </button>
          </div>

          <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
            <div class="p-6">
              <div class="space-y-4">
                <div
                  v-for="(spec, index) in specifications"
                  :key="spec.id"
                  class="grid grid-cols-12 gap-4 items-center p-4 bg-slate-50 rounded-lg"
                >
                  <div class="col-span-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Key</label>
                    <input
                      v-model="spec.key"
                      type="text"
                      placeholder="e.g., Material"
                      class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                    />
                  </div>
                  <div class="col-span-6">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Value</label>
                    <input
                      v-model="spec.value"
                      type="text"
                      placeholder="e.g., 100% Cotton"
                      class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                    />
                  </div>
                  <div class="col-span-1">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Order</label>
                    <input
                      v-model.number="spec.displayOrder"
                      type="number"
                      class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                    />
                  </div>
                  <div class="col-span-1 flex justify-center">
                    <button
                      @click="removeSpecification(spec.id)"
                      class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                    >
                      <Trash2Icon class="w-4 h-4" />
                    </button>
                  </div>
                </div>

                <div v-if="specifications.length === 0" class="text-center py-8">
                  <FileTextIcon class="w-12 h-12 text-slate-400 mx-auto mb-4" />
                  <h3 class="text-lg font-medium text-slate-900 mb-2">No specifications added</h3>
                  <p class="text-slate-600 mb-4">
                    Add product specifications to provide detailed information to customers
                  </p>
                  <button
                    @click="addSpecification"
                    class="flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors mx-auto"
                  >
                    <PlusIcon class="w-4 h-4" />
                    Add First Specification
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import {
  ArrowLeftIcon,
  SaveIcon,
  AlertCircleIcon,
  PackageIcon,
  DollarSignIcon,
  WarehouseIcon,
  PlusIcon,
  PaletteIcon,
  StarIcon,
  Trash2Icon,
  UploadIcon,
  ImageIcon,
  CheckIcon,
  FileTextIcon
} from 'lucide-vue-next'

// Reactive data
const activeTab = ref('basic')

const tabs = [
  { id: 'basic', label: 'Basic Info', icon: PackageIcon },
  { id: 'colors', label: 'Colors & Images', icon: PaletteIcon },
  { id: 'specifications', label: 'Specifications', icon: FileTextIcon }
]

const product = ref({
  name: 'Premium Cotton T-Shirt',
  categoryId: 'clothing',
  branchId: 'main',
  description: 'High-quality cotton t-shirt with premium fabric and comfortable fit.',
  price: 29.99,
  originalPrice: 39.99,
  stock: 100,
  isAvailable: true
})

const colors = ref([
  {
    id: '1',
    name: 'Navy Blue',
    colorCode: '#1e3a8a',
    priceAdjustment: 0,
    stock: 40,
    displayOrder: 1,
    image: '/placeholder.svg?height=400&width=300',
    isDefault: true,
    sizes: [
      { id: '1', name: 'Small', value: 'S', stock: 10, priceAdjustment: 0, isAvailable: true },
      { id: '2', name: 'Medium', value: 'M', stock: 15, priceAdjustment: 0, isAvailable: true },
      { id: '3', name: 'Large', value: 'L', stock: 15, priceAdjustment: 2, isAvailable: true }
    ]
  },
  {
    id: '2',
    name: 'Forest Green',
    colorCode: '#166534',
    priceAdjustment: 0,
    stock: 35,
    displayOrder: 2,
    image: '/placeholder.svg?height=400&width=300',
    isDefault: false,
    sizes: [
      { id: '4', name: 'Small', value: 'S', stock: 8, priceAdjustment: 0, isAvailable: true },
      { id: '5', name: 'Medium', value: 'M', stock: 12, priceAdjustment: 0, isAvailable: true },
      { id: '6', name: 'Large', value: 'L', stock: 15, priceAdjustment: 2, isAvailable: true }
    ]
  }
])

const specifications = ref([
  { id: '1', key: 'Material', value: '100% Cotton', displayOrder: 1 },
  { id: '2', key: 'Care Instructions', value: 'Machine wash cold', displayOrder: 2 },
  { id: '3', key: 'Origin', value: 'Made in USA', displayOrder: 3 }
])

// Computed properties
const totalAllocatedStock = computed(() => {
  return colors.value.reduce((total, color) => total + color.stock, 0)
})

const stockProgress = computed(() => {
  return (totalAllocatedStock.value / product.value.stock) * 100
})

// Methods
const addColor = () => {
  const newColor = {
    id: Date.now().toString(),
    name: '',
    colorCode: '#000000',
    priceAdjustment: 0,
    stock: 0,
    displayOrder: colors.value.length + 1,
    image: null,
    isDefault: false,
    sizes: []
  }
  colors.value.push(newColor)
}

const removeColor = (colorId) => {
  colors.value = colors.value.filter(color => color.id !== colorId)
}

const setDefaultColor = (colorId) => {
  colors.value = colors.value.map(color => ({
    ...color,
    isDefault: color.id === colorId
  }))
}

const triggerImageUpload = (colorId) => {
  const input = document.querySelector(`input[ref="fileInput-${colorId}"]`)
  if (input) {
    input.click()
  }
}

const handleImageUpload = (colorId, event) => {
  const file = event.target.files[0]
  if (file) {
    const imageUrl = URL.createObjectURL(file)
    const colorIndex = colors.value.findIndex(color => color.id === colorId)
    if (colorIndex !== -1) {
      colors.value[colorIndex].image = imageUrl
    }
  }
}

const addSpecification = () => {
  const newSpec = {
    id: Date.now().toString(),
    key: '',
    value: '',
    displayOrder: specifications.value.length + 1
  }
  specifications.value.push(newSpec)
}

const removeSpecification = (specId) => {
  specifications.value = specifications.value.filter(spec => spec.id !== specId)
}
</script>

<style scoped>
/* Custom styles for better visual appeal */
.transition-colors {
  transition: color 0.2s ease, background-color 0.2s ease, border-color 0.2s ease;
}

.transition-all {
  transition: all 0.2s ease;
}

/* Focus styles for better accessibility */
input:focus,
select:focus,
textarea:focus {
  outline: none;
}

/* Custom scrollbar for better UX */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>