import DataNotFoundError from "@/Components/Error/DataNotFoundError";
import DataLoading from "@/Components/Loading/DataLoading";
import { InputImageSliderModal } from "@/Components/Modal/InputImageSliderModal";
import ListDataPagination from "@/Components/Pagination/ListDataPagination";
import { PER_PAGES } from "@/Constants/dataOptions";
import useDeleteImageSlider from "@/Features/ImageSliders/useDeleteImageSlider";
import useGetImageSliders from "@/Features/ImageSliders/useGetImageSliders";
import BackpageLayout from "@/Layouts/BackpageLayout";
import { Link } from "@inertiajs/react";
import { Button, Label, Select, Table, TextInput } from "flowbite-react";
import { FaInfoCircle, FaTrash } from "react-icons/fa";

export default function ImageSliderPage() {
    const { imageSliders, isLoading, params, handleChange, getData } =
        useGetImageSliders();

    const { deleteDataConfirm } = useDeleteImageSlider();

    return (
        <BackpageLayout>
            <div className="w-full grid grid-cols-2 gap-2 sm:grid-cols-4 lg:grid-cols-6 items-end">
                <div>
                    <div className="mb-2 block">
                        <Label htmlFor="perpage" value="Perpage" />
                    </div>
                    <Select
                        id="perpage"
                        value={params.perpage}
                        onChange={(e) =>
                            handleChange("perpage", e.target.value)
                        }
                    >
                        {PER_PAGES.map((perpage) => (
                            <option key={perpage} value={perpage}>
                                {perpage}
                            </option>
                        ))}
                    </Select>
                </div>
                <div>
                    <div className="mb-2 block">
                        <Label htmlFor="link" value="Link" />
                    </div>
                    <TextInput
                        id="link"
                        type="search"
                        placeholder="Search by link..."
                        value={params.link}
                        onChange={(e) => handleChange("link", e.target.value)}
                    />
                </div>
                <div>
                    <div className="mb-2 block">
                        <Label htmlFor="index" value="Index" />
                    </div>
                    <TextInput
                        id="index"
                        type="search"
                        placeholder="Search by index..."
                        value={params.index}
                        onChange={(e) => handleChange("index", e.target.value)}
                    />
                </div>
                <div>
                    <div className="mb-2 block">
                        <Label htmlFor="is_active" value="Status" />
                    </div>
                    <Select
                        id="is_active"
                        value={params.is_active}
                        onChange={(e) =>
                            handleChange("is_active", e.target.value)
                        }
                    >
                        <option value="">All</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </Select>
                </div>
                <div className="flex gap-2">
                    <Button
                        onClick={getData}
                        color="none"
                        type="button"
                        className="bg-green-700/80 hover:bg-green-700/100 text-white text-nowrap w-fit"
                    >
                        Find
                    </Button>
                    <Link href="/image-sliders">
                        <Button
                            color="none"
                            type="button"
                            className="bg-red-700/80 hover:bg-red-700/100 text-white text-nowrap w-fit"
                        >
                            Reset
                        </Button>
                    </Link>

                    <InputImageSliderModal
                        trigger={
                            <Button
                                color="none"
                                type="button"
                                className="bg-primary hover:bg-yellow-500/100 text-white text-nowrap w-fit"
                            >
                                Add Data
                            </Button>
                        }
                    />
                </div>
            </div>
            <div className="mt-4 overflow-x-auto">
                <Table striped>
                    <Table.Head>
                        <Table.HeadCell className="w-5">#</Table.HeadCell>
                        <Table.HeadCell>Image</Table.HeadCell>
                        <Table.HeadCell>Link</Table.HeadCell>
                        <Table.HeadCell>Index</Table.HeadCell>
                        <Table.HeadCell>Status</Table.HeadCell>
                        <Table.HeadCell className="flex items-center justify-center">
                            Actions
                        </Table.HeadCell>
                    </Table.Head>
                    <Table.Body className="divide-y">
                        {!isLoading &&
                            imageSliders.data.map((imageSlider, index) => (
                                <Table.Row key={index} className="bg-white">
                                    <Table.Cell className="w-5 whitespace-nowrap font-medium text-gray-900">
                                        {(imageSliders.current_page - 1) *
                                            imageSliders.per_page +
                                            index +
                                            1}
                                    </Table.Cell>
                                    <Table.Cell className="whitespace-nowrap font-medium text-gray-900">
                                        <img
                                            src={imageSlider.image}
                                            alt="Image"
                                            className="w-28 rounded-lg object-cover"
                                        />
                                    </Table.Cell>
                                    <Table.Cell>
                                        {imageSlider.link ?? "-"}
                                    </Table.Cell>
                                    <Table.Cell>
                                        {imageSlider.index ?? "-"}
                                    </Table.Cell>
                                    <Table.Cell>
                                        {imageSlider.is_active
                                            ? "Active"
                                            : "Inactive"}
                                    </Table.Cell>
                                    <Table.Cell className="flex items-center justify-center gap-2">
                                        <InputImageSliderModal
                                            trigger={
                                                <FaInfoCircle className="size-6 text-blue-500" />
                                            }
                                            isUpdate={true}
                                            data={imageSlider}
                                        />
                                        <button
                                            onClick={() =>
                                                deleteDataConfirm(
                                                    imageSlider.id,
                                                )
                                            }
                                        >
                                            <FaTrash className="size-5 text-red-500" />
                                        </button>
                                    </Table.Cell>
                                </Table.Row>
                            ))}
                    </Table.Body>
                </Table>
                {isLoading && <DataLoading />}
                {imageSliders.data.length <= 0 && !isLoading && (
                    <DataNotFoundError />
                )}
            </div>
            {imageSliders.data.length > 0 && !isLoading && (
                <ListDataPagination data={imageSliders} params={params} />
            )}
        </BackpageLayout>
    );
}
