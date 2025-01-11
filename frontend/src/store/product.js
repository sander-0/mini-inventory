import { create } from "zustand";

const API_URL = "http://localhost/pweb_ujian/backend/crud.php";

export const useProductStore = create((set) => ({
  products: [],

  // Set the products state
  setProducts: (products) => set({ products }),

  // Fetch all products from the MySQL database
  fetchProducts: async () => {
    try {
      const res = await fetch(`${API_URL}?action=read`); // `action=read` indicates a GET request in your PHP
      const data = await res.json();
      if (data.success) {
        set({ products: data.data });
      } else {
        console.error("Failed to fetch products:", data.message);
      }
    } catch (error) {
      console.error("Error fetching products:", error);
    }
  },

  // Create a new product and add it to the database
  createProduct: async (newProduct) => {
    if (!newProduct.name || !newProduct.image || !newProduct.quantity) {
      return { success: false, message: "Please fill in all fields." };
    }
    try {
      const res = await fetch(`${API_URL}`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ action: "create", ...newProduct }),
      });
      const data = await res.json();
      if (data.success) {
        set((state) => ({ products: [...state.products, data.data] }));
        return { success: true, message: "Product created successfully" };
      } else {
        return { success: false, message: data.message };
      }
    } catch (error) {
      console.error("Error creating product:", error);
      return { success: false, message: "Error creating product." };
    }
  },

  // Delete a product from the MySQL database
  deleteProduct: async (pid) => {
    try {
      const res = await fetch(`${API_URL}`, {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ action: "delete", id: pid }),
      });
  
      const data = await res.json();
  
      if (!data.success) {
        return { success: false, message: data.message };
      }
  
      // Update state to remove the deleted product
      set((state) => ({
        products: state.products.filter((product) => product.id !== pid),
      }));
      return { success: true, message: data.message };
    } catch (error) {
      console.error("Error deleting product:", error);
      return { success: false, message: "Error deleting product." };
    }
  },
  

  updateProduct: async (pid, updatedProduct) => {
    try {
      const res = await fetch(`${API_URL}`, {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ action: "update", id: pid, ...updatedProduct }),
      });
      const data = await res.json();
  
      if (data.success) {
        set((state) => ({
            products: state.products.map((product) =>
              product.id === pid ? { ...product, ...updatedProduct } : product
            ),
        }));          
        return { success: true, message: "Product updated successfully" };
      } else {
        return { success: false, message: data.message };
      }
    } catch (error) {
      console.error("Error updating product:", error);
      return { success: false, message: "Error updating product." };
    }
  },  
}));
