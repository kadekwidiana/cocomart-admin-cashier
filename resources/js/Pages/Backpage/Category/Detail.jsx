import { AddCategoryImageModal } from "@/Components/Modal/AddCategoryImageModal";
import useDeleteCategoryImage from "@/Features/Categories/useDeleteCategoryImage";
import BackpageLayout from "@/Layouts/BackpageLayout";
import { formatDateToEnglish } from "@/Utils/formatDateToEnglish";
import { usePage } from "@inertiajs/react";
import { Button, Modal } from "flowbite-react";
import { useState } from "react";
import { FaInfoCircle } from "react-icons/fa";
import { FaTrash } from "react-icons/fa6";

export default function DetailCategoryPage() {
    const { category, images } = usePage().props;

    return (
        <BackpageLayout>
            <div>
                <div className="grid grid-cols-1 gap-2 lg:grid-cols-2">
                    <div className="relative overflow-x-auto">
                        <table className="w-full text-left text-sm text-gray-700 rtl:text-right">
                            <tbody>
                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">
                                        Category Name
                                    </td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {category.name}
                                    </td>
                                </tr>
                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">
                                        Category Id
                                    </td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {category.id}
                                    </td>
                                </tr>

                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">
                                        Category Code
                                    </td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {category.code}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <CategoryImages
                        images={images}
                        oxyCategoryId={category.categoryId}
                    />
                </div>
            </div>
        </BackpageLayout>
    );
}

function CategoryImages({ images, oxyCategoryId }) {
    const [openModal, setOpenModal] = useState(false);
    const [selectedImage, setSelectedImage] = useState(null);

    const handleOpenDetail = (image) => {
        setSelectedImage(image);
        setOpenModal(true);
    };

    const { deleteDataConfirm } = useDeleteCategoryImage();

    return (
        <>
            <div className="space-y-2">
                <AddCategoryImageModal
                    oxyCategoryId={oxyCategoryId}
                    trigger={
                        <Button
                            color="none"
                            type="button"
                            className="bg-primary hover:bg-yellow-500 text-white text-nowrap w-fit"
                        >
                            Add Image
                        </Button>
                    }
                />
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {images.length > 0 ? (
                        images.map((image) => (
                            <div
                                key={image.id}
                                className="border rounded-lg p-2 bg-white space-y-3"
                            >
                                <img
                                    src={image.image}
                                    alt="Category"
                                    className="w-full h-56 object-cover rounded"
                                />

                                <div className="flex justify-end gap-2">
                                    <button
                                        type="button"
                                        onClick={() => handleOpenDetail(image)}
                                    >
                                        <FaInfoCircle className="size-6 text-blue-500" />
                                    </button>

                                    <button
                                        type="button"
                                        onClick={() =>
                                            deleteDataConfirm(image.id)
                                        }
                                    >
                                        <FaTrash className="size-5 text-red-500" />
                                    </button>
                                </div>
                            </div>
                        ))
                    ) : (
                        <p className="text-gray-500">No image found</p>
                    )}
                </div>
            </div>

            <Modal
                show={openModal}
                onClose={() => setOpenModal(false)}
                size="lg"
            >
                <Modal.Header>Detail Image</Modal.Header>

                <Modal.Body>
                    {selectedImage && (
                        <div className="space-y-4">
                            <img
                                src={selectedImage.image}
                                alt="Detail"
                                className="w-full max-h-96 object-contain rounded"
                            />

                            <div className="text-sm text-gray-600">
                                <p>
                                    <span className="font-medium">
                                        Created At:
                                    </span>{" "}
                                    {formatDateToEnglish(
                                        selectedImage.created_at ?? "",
                                    )}
                                </p>
                                <p>
                                    <span className="font-medium">
                                        Updated At:
                                    </span>{" "}
                                    {formatDateToEnglish(
                                        selectedImage.updated_at ?? "",
                                    )}
                                </p>
                            </div>
                        </div>
                    )}
                </Modal.Body>
            </Modal>
        </>
    );
}
