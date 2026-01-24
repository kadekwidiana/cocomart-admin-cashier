import DataNotFoundError from "@/Components/Error/DataNotFoundError";
import DataLoading from "@/Components/Loading/DataLoading";
import { InputPromoModal } from "@/Components/Modal/InputPromoModal";
import ListDataPagination from "@/Components/Pagination/ListDataPagination";
import { PER_PAGES } from "@/Constants/dataOptions";
import useDeletePromo from "@/Features/Promos/useDeletePromo";
import useGetPromos from "@/Features/Promos/useGetPromos";
import BackpageLayout from "@/Layouts/BackpageLayout";
import { formatDateToIndonesian } from "@/Utils/formatDateToIndonesian";
import { Link } from "@inertiajs/react";
import { format } from "date-fns";
import { Button, Label, Select, Table, TextInput } from "flowbite-react";
import { FaInfoCircle, FaTrash } from "react-icons/fa";

export default function PromoPage() {
    const { promos, isLoading, params, handleChange, getData } = useGetPromos();

    const { deleteDataConfirm } = useDeletePromo();

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
                        <Label htmlFor="title" value="Title" />
                    </div>
                    <TextInput
                        id="title"
                        type="search"
                        placeholder="Search by title..."
                        value={params.title}
                        onChange={(e) => handleChange("title", e.target.value)}
                    />
                </div>
                <div>
                    <div className="mb-2 block">
                        <Label htmlFor="code" value="Code" />
                    </div>
                    <TextInput
                        id="code"
                        type="search"
                        placeholder="Search by code..."
                        value={params.code}
                        onChange={(e) => handleChange("code", e.target.value)}
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
                        <option value="">Semua</option>
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </Select>
                </div>
                <div>
                    <div className="mb-2 block">
                        <Label htmlFor="start_date" value="Start Date" />
                    </div>
                    <TextInput
                        id="start_date"
                        type="date"
                        placeholder="Search by start_date..."
                        value={params.start_date}
                        onChange={(e) =>
                            handleChange("start_date", e.target.value)
                        }
                    />
                </div>
                <div>
                    <div className="mb-2 block">
                        <Label htmlFor="end_date" value="End Date" />
                    </div>
                    <TextInput
                        id="end_date"
                        type="date"
                        placeholder="Search by end_date..."
                        value={params.end_date}
                        onChange={(e) =>
                            handleChange("end_date", e.target.value)
                        }
                    />
                </div>
                <div className="flex gap-2">
                    <Button
                        onClick={getData}
                        color="none"
                        type="button"
                        className="bg-green-700/80 hover:bg-green-700/100 text-white text-nowrap w-fit"
                    >
                        Cari
                    </Button>
                    <Link href="/promos">
                        <Button
                            color="none"
                            type="button"
                            className="bg-red-700/80 hover:bg-red-700/100 text-white text-nowrap w-fit"
                        >
                            Reset
                        </Button>
                    </Link>

                    <InputPromoModal
                        trigger={
                            <Button
                                color="none"
                                type="button"
                                className="bg-primary/80 hover:bg-primary/100 text-white text-nowrap w-fit"
                            >
                                Tambah Data
                            </Button>
                        }
                    />
                </div>
            </div>
            <div className="mt-4 overflow-x-auto">
                <Table striped>
                    <Table.Head>
                        <Table.HeadCell className="w-5">#</Table.HeadCell>
                        <Table.HeadCell>Title</Table.HeadCell>
                        <Table.HeadCell>Code</Table.HeadCell>
                        <Table.HeadCell>Discount</Table.HeadCell>
                        <Table.HeadCell>Status</Table.HeadCell>
                        <Table.HeadCell>Start Date</Table.HeadCell>
                        <Table.HeadCell>End Date</Table.HeadCell>
                        <Table.HeadCell className="flex items-center justify-center">
                            Aksi
                        </Table.HeadCell>
                    </Table.Head>
                    <Table.Body className="divide-y">
                        {!isLoading &&
                            promos.data.map((promo, index) => (
                                <Table.Row key={index} className="bg-white">
                                    <Table.Cell className="w-5 whitespace-nowrap font-medium text-gray-900">
                                        {(promos.current_page - 1) *
                                            promos.per_page +
                                            index +
                                            1}
                                    </Table.Cell>
                                    <Table.Cell className="whitespace-nowrap font-medium text-gray-900">
                                        {promo.title ?? "-"}
                                    </Table.Cell>
                                    <Table.Cell>{promo.code ?? "-"}</Table.Cell>
                                    <Table.Cell>
                                        {`${promo.discount_percentage} %` ??
                                            "-"}
                                    </Table.Cell>
                                    <Table.Cell>
                                        {promo.is_active
                                            ? "Aktif"
                                            : "Tidak Aktif"}
                                    </Table.Cell>
                                    <Table.Cell>
                                        {formatDateToIndonesian(
                                            promo.start_date ?? "",
                                        )}
                                    </Table.Cell>
                                    <Table.Cell>
                                        {formatDateToIndonesian(
                                            promo.end_date ?? "",
                                        )}
                                    </Table.Cell>
                                    <Table.Cell className="flex items-center justify-center gap-2">
                                        <InputPromoModal
                                            trigger={
                                                <FaInfoCircle className="size-6 text-blue-500" />
                                            }
                                            isUpdate={true}
                                            data={promo}
                                        />
                                        <button
                                            onClick={() =>
                                                deleteDataConfirm(promo.id)
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
                {promos.data.length <= 0 && !isLoading && <DataNotFoundError />}
            </div>
            {promos.data.length > 0 && !isLoading && (
                <ListDataPagination data={promos} params={params} />
            )}
        </BackpageLayout>
    );
}
