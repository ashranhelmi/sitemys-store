import { useState } from "react";
import { useForm } from "@inertiajs/react";
import AppLayout from "@/components/layout/AppLayout";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Badge } from "@/components/ui/badge";
import { Dialog, DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { Plus, Pencil, Trash2 } from "lucide-react";

const SIZES = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'Free Size'];

function ProductForm({ product, onClose }) {
    const { data, setData, post, put, processing, errors, reset } = useForm({
        name: product?.name ?? '',
        description: product?.description ?? '',
        price: product?.price ?? '',
        stock: product?.stock ?? 0,
        sizes: product?.sizes ?? [],
        is_active: product?.is_active ?? true,
    });

    const toggleSize = (size) => {
        setData('sizes', data.sizes.includes(size)
            ? data.sizes.filter(s => s !== size)
            : [...data.sizes, size]);
    };

    const submit = (e) => {
        e.preventDefault();
        if (product) {
            put(route('admin.products.update', product.id), { onSuccess: () => { reset(); onClose(); } });
        } else {
            post(route('admin.products.store'), { onSuccess: () => { reset(); onClose(); } });
        }
    };

    return (
        <form onSubmit={submit} className="space-y-4">
            <div className="grid grid-cols-2 gap-4">
                <div className="col-span-2 space-y-1">
                    <Label>Product Name</Label>
                    <Input value={data.name} onChange={e => setData('name', e.target.value)} required />
                    {errors.name && <p className="text-red-400 text-xs">{errors.name}</p>}
                </div>
                <div className="space-y-1">
                    <Label>Price (RM)</Label>
                    <Input type="number" step="0.01" min="0" value={data.price} onChange={e => setData('price', e.target.value)} required />
                    {errors.price && <p className="text-red-400 text-xs">{errors.price}</p>}
                </div>
                <div className="space-y-1">
                    <Label>Stock</Label>
                    <Input type="number" min="0" value={data.stock} onChange={e => setData('stock', e.target.value)} required />
                </div>
                <div className="col-span-2 space-y-1">
                    <Label>Description</Label>
                    <textarea value={data.description} onChange={e => setData('description', e.target.value)}
                        rows={3} className="w-full bg-background border border-input rounded-md px-3 py-2 text-sm resize-none focus:outline-none focus:ring-1 focus:ring-ring" />
                </div>
                <div className="col-span-2 space-y-2">
                    <Label>Sizes</Label>
                    <div className="flex flex-wrap gap-2">
                        {SIZES.map(size => (
                            <button type="button" key={size} onClick={() => toggleSize(size)}
                                className={`px-3 py-1 text-xs border rounded transition ${data.sizes.includes(size) ? 'bg-primary text-primary-foreground border-primary' : 'border-input text-muted-foreground hover:border-foreground'}`}>
                                {size}
                            </button>
                        ))}
                    </div>
                </div>
                <div className="col-span-2 flex items-center gap-2">
                    <input type="checkbox" id="is_active" checked={data.is_active} onChange={e => setData('is_active', e.target.checked)} className="size-4" />
                    <Label htmlFor="is_active">Active (visible in store)</Label>
                </div>
            </div>
            <div className="flex gap-2 justify-end pt-2">
                <Button type="button" variant="outline" onClick={onClose}>Cancel</Button>
                <Button type="submit" disabled={processing}>{product ? 'Update' : 'Create'} Product</Button>
            </div>
        </form>
    );
}

export default function ProductsIndex({ products }) {
    const [open, setOpen] = useState(false);
    const [editing, setEditing] = useState(null);
    const { delete: destroy } = useForm();

    const handleDelete = (product) => {
        if (!confirm(`Delete "${product.name}"?`)) return;
        destroy(route('admin.products.destroy', product.id));
    };

    return (
        <AppLayout title="Products" breadcrumbs={[{ label: "Dashboard", url: route('dashboard') }, { label: "Products" }]}>
            <div className="flex items-center justify-between mb-6">
                <h1 className="text-xl font-semibold">Products</h1>
                <Button onClick={() => { setEditing(null); setOpen(true); }}>
                    <Plus className="size-4 mr-2" /> Add Product
                </Button>
            </div>

            <Card>
                <CardContent className="p-0">
                    {products.length === 0 ? (
                        <p className="text-sm text-muted-foreground p-6">No products yet. Add your first product.</p>
                    ) : (
                        <table className="w-full text-sm">
                            <thead className="border-b">
                                <tr className="text-muted-foreground text-xs uppercase tracking-wider">
                                    <th className="text-left p-4">Product</th>
                                    <th className="text-left p-4">Price</th>
                                    <th className="text-left p-4">Stock</th>
                                    <th className="text-left p-4">Sizes</th>
                                    <th className="text-left p-4">Status</th>
                                    <th className="p-4"></th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border">
                                {products.map(product => (
                                    <tr key={product.id} className="hover:bg-muted/30 transition">
                                        <td className="p-4 font-medium">{product.name}</td>
                                        <td className="p-4">RM {parseFloat(product.price).toFixed(2)}</td>
                                        <td className="p-4">{product.stock}</td>
                                        <td className="p-4">
                                            <div className="flex flex-wrap gap-1">
                                                {(product.sizes ?? []).map(s => <Badge key={s} variant="outline" className="text-xs">{s}</Badge>)}
                                            </div>
                                        </td>
                                        <td className="p-4">
                                            <Badge variant={product.is_active ? 'default' : 'secondary'}>
                                                {product.is_active ? 'Active' : 'Hidden'}
                                            </Badge>
                                        </td>
                                        <td className="p-4">
                                            <div className="flex gap-2 justify-end">
                                                <Button size="icon" variant="ghost" onClick={() => { setEditing(product); setOpen(true); }}>
                                                    <Pencil className="size-4" />
                                                </Button>
                                                <Button size="icon" variant="ghost" className="text-destructive hover:text-destructive" onClick={() => handleDelete(product)}>
                                                    <Trash2 className="size-4" />
                                                </Button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    )}
                </CardContent>
            </Card>

            <Dialog open={open} onOpenChange={setOpen}>
                <DialogContent className="max-w-lg">
                    <DialogHeader>
                        <DialogTitle>{editing ? 'Edit Product' : 'Add Product'}</DialogTitle>
                    </DialogHeader>
                    <ProductForm product={editing} onClose={() => setOpen(false)} />
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
