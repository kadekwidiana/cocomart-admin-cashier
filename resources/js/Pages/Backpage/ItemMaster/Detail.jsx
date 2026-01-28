import DataNotFoundError from "@/Components/Error/DataNotFoundError";
import { AddItemMasterImageModal } from "@/Components/Modal/AddItemMasterImageModal";
import { DetailImageModal } from "@/Components/Modal/DetailImageModal";
import useDeleteItemMasterImage from "@/Features/ItemMasters/useDeleteItemMasterImage";
import BackpageLayout from "@/Layouts/BackpageLayout";
import { formatRupiah } from "@/Utils/formatNumber";
import { usePage } from "@inertiajs/react";
import { Button, Table, TextInput } from "flowbite-react";
import { useEffect, useState } from "react";
import { FaInfoCircle } from "react-icons/fa";
import { FaTrash } from "react-icons/fa6";

export default function DetailItemMasterPage() {
    const { itemMaster, images, locations } = usePage().props;

    const [locationsFiltered, setLocationsFiltered] = useState(locations);

    const [searchLocation, setSearchLocation] = useState("");

    useEffect(() => {
        setLocationsFiltered(
            locations.filter((location) =>
                location.name
                    .toLowerCase()
                    .includes(searchLocation.toLowerCase()),
            ),
        );
    }, [searchLocation]);

    return (
        <BackpageLayout>
            <div>
                <div className="grid grid-cols-1 gap-2 lg:grid-cols-2">
                    <div className="relative overflow-x-auto">
                        <table className="w-full text-left text-sm text-gray-700 rtl:text-right">
                            <tbody>
                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">Name</td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {itemMaster.name}
                                    </td>
                                </tr>
                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">Id</td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {itemMaster.itemMasterId}
                                    </td>
                                </tr>

                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">Code</td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {itemMaster.code}
                                    </td>
                                </tr>
                                <tr className="bg-white">
                                    <td className="w-1/5 py-2 pr-2">Barcode</td>
                                    <td className="w-3 px-2 py-2">:</td>
                                    <td className="w-full px-2 py-2">
                                        {itemMaster.barcode}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <ItemMasterImages
                        images={images}
                        oxyItemMasterId={itemMaster.itemMasterId}
                    />
                    <div className="col-span-1 lg:col-span-2 space-y-2 mb-2">
                        <h2 className="text-lg text-gray-800">
                            Stock and Price per Location
                        </h2>
                        <TextInput
                            id="location"
                            type="search"
                            placeholder="Search location..."
                            value={searchLocation}
                            onChange={(e) => setSearchLocation(e.target.value)}
                            className="w-full lg:w-1/2"
                        />
                    </div>
                    <div className="col-span-1 lg:col-span-2">
                        <Table striped>
                            <Table.Head>
                                <Table.HeadCell className="w-5">
                                    #
                                </Table.HeadCell>
                                <Table.HeadCell>Location</Table.HeadCell>
                                <Table.HeadCell>Code</Table.HeadCell>
                                <Table.HeadCell>Stock</Table.HeadCell>
                                <Table.HeadCell>Price</Table.HeadCell>
                            </Table.Head>
                            <Table.Body className="divide-y">
                                {locationsFiltered.length > 0 &&
                                    locationsFiltered.map((location, index) => (
                                        <Table.Row
                                            key={index}
                                            className="bg-white"
                                        >
                                            <Table.Cell className="w-5 whitespace-nowrap font-medium text-gray-900">
                                                {index + 1}
                                            </Table.Cell>
                                            <Table.Cell className="whitespace-nowrap font-medium text-gray-900">
                                                {location.name ?? "-"}
                                            </Table.Cell>
                                            <Table.Cell>
                                                {location.code ?? "-"}
                                            </Table.Cell>
                                            <Table.Cell>
                                                {(() => {
                                                    const qty =
                                                        itemMaster.stocks.find(
                                                            (s) =>
                                                                s.locationId ===
                                                                location.id,
                                                        )?.qty;

                                                    return qty != null
                                                        ? qty
                                                        : "-";
                                                })()}
                                            </Table.Cell>
                                            <Table.Cell>
                                                {(() => {
                                                    const price =
                                                        itemMaster.prices.find(
                                                            (s) =>
                                                                s.locationId ===
                                                                location.id,
                                                        )?.sellingPrice;

                                                    return price != null
                                                        ? formatRupiah(price)
                                                        : "-";
                                                })()}
                                            </Table.Cell>
                                        </Table.Row>
                                    ))}
                            </Table.Body>
                        </Table>
                        {locationsFiltered.length <= 0 && <DataNotFoundError />}
                    </div>
                </div>
            </div>
        </BackpageLayout>
    );
}

function ItemMasterImages({ images, oxyItemMasterId }) {
    const { deleteDataConfirm } = useDeleteItemMasterImage();

    return (
        <>
            <div className="space-y-2">
                <AddItemMasterImageModal
                    oxyItemMasterId={oxyItemMasterId}
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
                                    alt="Item Master"
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
