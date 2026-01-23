import DataNotFoundError from "@/Components/Error/DataNotFoundError";
import DataLoading from "@/Components/Loading/DataLoading";
import { InputImageSliderModal } from "@/Components/Modal/InputImageSliderModal";
import ListDataPagination from "@/Components/Pagination/ListDataPagination";
import { PER_PAGES } from "@/Constants/dataOptions";
import useDeleteImageSlider from "@/Features/ImageSliders/useDeleteImageSlider";
import useGetImageSliders from "@/Features/ImageSliders/useGetImageSliders";
import BackpageLayout from "@/Layouts/BackpageLayout";
import { Button, Select, Table, TextInput } from "flowbite-react";
import { FaInfoCircle, FaTrash } from "react-icons/fa";

export default function ImageSliderPage() {
    const {
        imageSliders,
        isLoading,
        perpage,
        searchValue,
        debouncedHandleSearch,
        handleChangePerPage,
    } = useGetImageSliders();

    const { deleteDataConfirm } = useDeleteImageSlider();

    console.log(imageSliders);

    return (
        <BackpageLayout>
            <div className="flex w-full flex-col items-start justify-start gap-4 md:flex-row md:items-center md:gap-2">
                <Select
                    defaultValue={perpage.current}
                    onChange={handleChangePerPage}
                    id="per-page"
                    required
                    className="min-w-20 max-w-20"
                >
                    {PER_PAGES.map((perPage) => (
                        <option key={perPage} value={perPage}>
                            {perPage}
                        </option>
                    ))}
                </Select>
                <TextInput
                    id="base"
                    type="search"
                    placeholder="Cari data..."
                    sizing="md"
                    className="w-full"
                    defaultValue={searchValue}
                    onChange={debouncedHandleSearch}
                />
                <InputImageSliderModal
                    trigger={
                        <Button
                            color="none"
                            className="bg-primary/80 hover:bg-primary/100 text-white text-nowrap"
                        >
                            Tambah Data
                        </Button>
                    }
                />
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
                            Aksi
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
                                            ? "Aktif"
                                            : "Tidak Aktif"}
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
                <ListDataPagination
                    data={imageSliders}
                    params={{
                        perpage: perpage.current || 10,
                        search: searchValue || "",
                    }}
                />
            )}
        </BackpageLayout>
    );
}
