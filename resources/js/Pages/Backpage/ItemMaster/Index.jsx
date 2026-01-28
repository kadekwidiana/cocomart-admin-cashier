import DataNotFoundError from "@/Components/Error/DataNotFoundError";
import DataLoading from "@/Components/Loading/DataLoading";
import ListDataPagination from "@/Components/Pagination/ListDataPagination";
import { PER_PAGES } from "@/Constants/dataOptions";
import useGetItemMasters from "@/Features/ItemMasters/useGetItemMasters";
import BackpageLayout from "@/Layouts/BackpageLayout";
import { Link } from "@inertiajs/react";
import { Button, Label, Select, Table, TextInput } from "flowbite-react";
import { FaInfoCircle } from "react-icons/fa";

export default function ItemMasterPage() {
    const { itemMasters, isLoading, params, handleChange, getData } =
        useGetItemMasters();

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
                        <Label htmlFor="name" value="Name" />
                    </div>
                    <TextInput
                        id="name"
                        type="search"
                        placeholder="Search by name..."
                        value={params.name}
                        onChange={(e) => handleChange("name", e.target.value)}
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
                <div className="flex gap-2">
                    <Button
                        onClick={getData}
                        color="none"
                        type="button"
                        className="bg-green-700/80 hover:bg-green-700/100 text-white text-nowrap w-fit"
                    >
                        Find
                    </Button>
                    <Link href="/item-masters">
                        <Button
                            color="none"
                            type="button"
                            className="bg-red-700/80 hover:bg-red-700/100 text-white text-nowrap w-fit"
                        >
                            Reset
                        </Button>
                    </Link>
                </div>
            </div>
            <div className="mt-4 overflow-x-auto">
                <Table striped>
                    <Table.Head>
                        <Table.HeadCell className="w-5">#</Table.HeadCell>
                        <Table.HeadCell>Name</Table.HeadCell>
                        <Table.HeadCell>Id</Table.HeadCell>
                        <Table.HeadCell>Code</Table.HeadCell>
                        <Table.HeadCell>Barcode</Table.HeadCell>
                        <Table.HeadCell className="flex items-center justify-center">
                            Actions
                        </Table.HeadCell>
                    </Table.Head>
                    <Table.Body className="divide-y">
                        {!isLoading &&
                            itemMasters.data.map((item, index) => (
                                <Table.Row key={index} className="bg-white">
                                    <Table.Cell className="w-5 whitespace-nowrap font-medium text-gray-900">
                                        {(itemMasters.current_page - 1) *
                                            itemMasters.per_page +
                                            index +
                                            1}
                                    </Table.Cell>
                                    <Table.Cell className="whitespace-nowrap font-medium text-gray-900">
                                        {item.name ?? "-"}
                                    </Table.Cell>
                                    <Table.Cell>{item.id ?? "-"}</Table.Cell>
                                    <Table.Cell>{item.code ?? "-"}</Table.Cell>
                                    <Table.Cell>
                                        {item.barcode ?? "-"}
                                    </Table.Cell>
                                    <Table.Cell className="flex items-center justify-center gap-2">
                                        <Link
                                            href={`/item-masters/${item.itemMasterId}`}
                                        >
                                            <FaInfoCircle className="size-6 text-blue-500" />
                                        </Link>
                                    </Table.Cell>
                                </Table.Row>
                            ))}
                    </Table.Body>
                </Table>
                {isLoading && <DataLoading />}
                {itemMasters.data.length <= 0 && !isLoading && (
                    <DataNotFoundError />
                )}
            </div>
            {itemMasters.data.length > 0 && !isLoading && (
                <ListDataPagination data={itemMasters} params={params} />
            )}
        </BackpageLayout>
    );
}
