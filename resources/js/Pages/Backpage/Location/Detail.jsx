import { AddLocationImageModal } from "@/Components/Modal/AddLocationImageModal";
import { DetailImageModal } from "@/Components/Modal/DetailImageModal";
import useDeleteLocationImage from "@/Features/Locations/useDeleteLocationImage";
import BackpageLayout from "@/Layouts/BackpageLayout";
import { formatDateToEnglish } from "@/Utils/formatDateToEnglish";
import { usePage } from "@inertiajs/react";
import { Button, Modal } from "flowbite-react";
import { useState } from "react";
import { FaInfoCircle } from "react-icons/fa";
import { FaTrash } from "react-icons/fa6";

export default function DetailLocationPage() {
    const { location, images } = usePage().props;

    return (
        <BackpageLayout>
            <div>
                <div className="grid grid-cols-1 gap-2 lg:grid-cols-2">
                    <div className="relative overflow-x-auto">
                        <table className="w-full text-left text-sm text-gray-700 rtl:text-right">
                            <tbody>
                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">
                                        Location Name
                                    </td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {location.name}
                                    </td>
                                </tr>
                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">
                                        Location Id
                                    </td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {location.id}
                                    </td>
                                </tr>

                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">
                                        Location Code
                                    </td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {location.code}
                                    </td>
                                </tr>
                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">
                                        Location Type
                                    </td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {location.type}
                                    </td>
                                </tr>
                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">
                                        Location Address Street
                                    </td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {location.addressStreet || "-"}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <LocationImages
                        images={images}
                        oxyLocationId={location.id}
                    />
                </div>
            </div>
        </BackpageLayout>
    );
}

function LocationImages({ images, oxyLocationId }) {
    const { deleteDataConfirm } = useDeleteLocationImage();

    return (
        <>
            <div className="space-y-2">
                <AddLocationImageModal
                    oxyLocationId={oxyLocationId}
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
                                    alt="Location"
                                    className="w-full h-56 object-cover rounded"
                                />

                                <div className="flex justify-end gap-2">
                                    <DetailImageModal
                                        image={image}
                                        trigger={
                                            <FaInfoCircle className="size-6 text-blue-500" />
                                        }
                                    />

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
        </>
    );
}
