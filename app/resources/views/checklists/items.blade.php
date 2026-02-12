@extends('layouts.dashboard')

@section('title', 'Checklist Items')
@section('subtitle', 'Manage CAPSTONE 2 completion requirements')

@section('content')
<div x-data="checklistItemsData()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 animate-slide-up">
        <div>
            <a href="{{ route('checklists.index') }}" class="inline-flex items-center text-emerald-600 hover:text-emerald-700 mb-2 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Checklists
            </a>
        </div>
        <button @click.stop.prevent="showAddModal = true" type="button" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-medium rounded-xl shadow-lg shadow-emerald-500/30 hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Checklist Item
        </button>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="text-center py-16">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-500 mx-auto"></div>
        <p class="mt-4 text-gray-500">Loading checklist items...</p>
    </div>

    <!-- Items List -->
    <div x-show="!loading" class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="divide-y divide-gray-100">
            <template x-for="(item, index) in items" :key="item.id">
                <div class="p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-xl flex items-center justify-center text-white font-bold mr-4"
                                 x-text="index + 1"></div>
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <p class="font-semibold text-gray-900 mr-2" x-text="item.name"></p>
                                    <span x-show="item.is_required" class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700">Required</span>
                                    <span x-show="!item.is_required" class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Optional</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1" x-text="item.description || 'No description'"></p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button @click.stop="editItem(item)" class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button @click.stop="confirmDelete(item)" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Empty State -->
    <div x-show="!loading && items.length === 0" class="text-center py-16 bg-white rounded-xl border border-gray-100">
        <div class="w-20 h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No checklist items yet</h3>
        <p class="text-gray-500 mb-6">Create your first checklist item to get started.</p>
        <button @click.stop.prevent="showAddModal = true" type="button" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-medium rounded-xl">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add First Item
        </button>
    </div>

    <!-- Add/Edit Modal -->
    <div x-show="showAddModal || showEditModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50"
         @click.self="closeModals()">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4 shadow-2xl">
            <h3 class="text-xl font-bold text-gray-900 mb-4" x-text="showEditModal ? 'Edit Checklist Item' : 'Add Checklist Item'"></h3>
            
            <form @submit.stop.prevent="showEditModal ? updateItem() : saveItem()">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Item Name</label>
                        <input type="text" x-model="form.name" 
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"
                               placeholder="e.g., Submit Hard Bound Copy" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Description (Optional)</label>
                        <textarea x-model="form.description" rows="3"
                                  class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 resize-none"
                                  placeholder="Additional details about this requirement..."></textarea>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" x-model="form.is_required" id="is_required"
                               class="w-5 h-5 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                        <label for="is_required" class="ml-2 text-sm font-medium text-gray-700">This is a required item</label>
                    </div>
                </div>

                <div class="mt-6 flex space-x-3">
                    <button type="button" @click.stop="closeModals()" 
                            class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2.5 bg-emerald-600 text-white font-medium rounded-xl hover:bg-emerald-700 transition-colors"
                            x-text="showEditModal ? 'Update' : 'Add Item'">
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50"
         @click.self="showDeleteModal = false">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4 shadow-2xl">
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Delete Checklist Item?</h3>
                <p class="text-gray-500 mb-6">This will deactivate the item. Existing completion records will be preserved.</p>
                <div class="flex space-x-3 justify-center">
                    <button @click.stop="showDeleteModal = false" class="px-6 py-2 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors font-medium">
                        Cancel
                    </button>
                    <button @click.stop="deleteItem()" class="px-6 py-2 bg-red-500 text-white rounded-xl hover:bg-red-600 transition-colors font-medium">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function checklistItemsData() {
    return {
        items: [],
        loading: true,
        showAddModal: false,
        showEditModal: false,
        showDeleteModal: false,
        itemToDelete: null,
        editingItem: null,
        form: {
            name: '',
            description: '',
            is_required: true
        },

        init() {
            this.loadItems();
        },

        async loadItems() {
            try {
                const response = await fetch('/api/checklist-items');
                this.items = await response.json();
            } catch (error) {
                console.error('Error loading items:', error);
            } finally {
                this.loading = false;
            }
        },

        closeModals() {
            this.showAddModal = false;
            this.showEditModal = false;
            this.editingItem = null;
            this.form = { name: '', description: '', is_required: true };
        },

        editItem(item) {
            this.editingItem = item;
            this.form = {
                name: item.name,
                description: item.description || '',
                is_required: item.is_required
            };
            this.showEditModal = true;
        },

        async saveItem() {
            try {
                const response = await fetch('/api/checklist-items', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                });

                if (response.ok) {
                    const newItem = await response.json();
                    this.items.push(newItem);
                    this.closeModals();
                }
            } catch (error) {
                console.error('Error saving item:', error);
            }
        },

        async updateItem() {
            if (!this.editingItem) return;

            try {
                const response = await fetch('/api/checklist-items/' + this.editingItem.id, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                });

                if (response.ok) {
                    const updatedItem = await response.json();
                    const index = this.items.findIndex(i => i.id === this.editingItem.id);
                    if (index !== -1) {
                        this.items[index] = updatedItem;
                    }
                    this.closeModals();
                }
            } catch (error) {
                console.error('Error updating item:', error);
            }
        },

        confirmDelete(item) {
            this.itemToDelete = item;
            this.showDeleteModal = true;
        },

        async deleteItem() {
            if (!this.itemToDelete) return;

            try {
                const response = await fetch('/api/checklist-items/' + this.itemToDelete.id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    this.items = this.items.filter(i => i.id !== this.itemToDelete.id);
                    this.showDeleteModal = false;
                    this.itemToDelete = null;
                }
            } catch (error) {
                console.error('Error deleting item:', error);
            }
        }
    }
}
</script>
@endsection
