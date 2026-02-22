import DataNotFoundError from "@/Components/Error/DataNotFoundError";
import DataLoading from "@/Components/Loading/DataLoading";
import ListDataPagination from "@/Components/Pagination/ListDataPagination";
import {
    PER_PAGES,
    TRANSACTION_FULFILLMENT_TYPES,
    TRANSACTION_STATUSES,
} from "@/Constants/dataOptions";
import useGetTransactions from "@/Features/Transactions/useGetTransactions";
import BackpageLayout from "@/Layouts/BackpageLayout";
import { formatDateToEnglish } from "@/Utils/formatDateToEnglish";
import { formatRupiah } from "@/Utils/formatNumber";
import { Link, usePage } from "@inertiajs/react";
import { Button, Label, Select, Table, TextInput } from "flowbite-react";
import { FaInfoCircle } from "react-icons/fa";

export default function TransactionPage() {
    const { auth } = usePage().props;
    const { locations } = usePage().props;

    const { transactions, isLoading, params, handleChange, getData } =
        useGetTransactions();

    console.log(auth);

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
                        <Label htmlFor="id" value="Id" />
                    </div>
                    <TextInput
                        id="id"
                        type="search"
                        placeholder="Search by id..."
                        value={params.id}
                        onChange={(e) => handleChange("id", e.target.value)}
                    />
                </div>
                <div>
                    <div className="mb-2 block">
                        <Label
                            htmlFor="fulfillment_type"
                            value="Fulfillment Type"
                        />
                    </div>
                    <Select
                        id="fulfillment_type"
                        name="fulfillment_type"
                        value={params.fulfillment_type}
                        onChange={(e) =>
                            handleChange("fulfillment_type", e.target.value)
                        }
                    >
                        <option value="">All</option>
                        {TRANSACTION_FULFILLMENT_TYPES.map(
                            (fulfillment_type) => (
                                <option
                                    key={fulfillment_type}
                                    value={fulfillment_type}
                                >
                                    {fulfillment_type}
                                </option>
                            ),
                        )}
                    </Select>
                </div>
                <div>
                    <div className="mb-2 block">
                        <Label htmlFor="status" value="Status" />
                    </div>
                    <Select
                        id="status"
                        value={params.status}
                        onChange={(e) => handleChange("status", e.target.value)}
                    >
                        <option value="">All</option>
                        {TRANSACTION_STATUSES.map((status) => (
                            <option key={status} value={status}>
                                {status}
                            </option>
                        ))}
                    </Select>
                </div>
                <div>
                    <div className="mb-2 block">
                        <Label htmlFor="oxy_location_id" value="Location" />
                    </div>
                    {auth.user.role === "CASHIER" ? (
                        <TextInput
                            id="oxy_location_id"
                            type="text"
                            value={
                                locations.find(
                                    (location) =>
                                        location.id === params.oxy_location_id,
                                )?.name ?? "-"
                            }
                            readOnly
                        />
                    ) : (
                        <Select
                            id="oxy_location_id"
                            value={params.oxy_location_id}
                            onChange={(e) =>
                                handleChange("oxy_location_id", e.target.value)
                            }
                        >
                            <option value="">All</option>
                            {locations.length > 0 &&
                                locations.map((location) => (
                                    <option
                                        key={location.id}
                                        value={location.id}
                                    >
                                        {location.name}
                                    </option>
                                ))}
                        </Select>
                    )}
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
                        Find
                    </Button>
                    <Link href="/transactions">
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
                        <Table.HeadCell>Id</Table.HeadCell>
                        <Table.HeadCell>Customer Id</Table.HeadCell>
                        <Table.HeadCell>Location</Table.HeadCell>
                        <Table.HeadCell>Status</Table.HeadCell>
                        <Table.HeadCell>Fulfillment Type</Table.HeadCell>
                        <Table.HeadCell>Total</Table.HeadCell>
                        <Table.HeadCell>Created At</Table.HeadCell>
                        <Table.HeadCell className="flex items-center justify-center">
                            Actions
                        </Table.HeadCell>
                    </Table.Head>
                    <Table.Body className="divide-y">
                        {!isLoading &&
                            transactions.data.map((transaction, index) => (
                                <Table.Row key={index} className="bg-white">
                                    <Table.Cell className="w-5 whitespace-nowrap font-medium text-gray-900">
                                        {(transactions.current_page - 1) *
                                            transactions.per_page +
                                            index +
                                            1}
                                    </Table.Cell>
                                    <Table.Cell className="whitespace-nowrap font-medium text-gray-900">
                                        {transaction.id}
                                    </Table.Cell>
                                    <Table.Cell>
                                        {transaction.oxy_customer_id}
                                    </Table.Cell>
                                    <Table.Cell>
                                        {locations.find(
                                            (location) =>
                                                location.id ===
                                                transaction.oxy_location_id,
                                        )?.name ?? "-"}
                                    </Table.Cell>
                                    <Table.Cell>
                                        {transaction.status}
                                    </Table.Cell>
                                    <Table.Cell>
                                        {transaction.fulfillment_type}
                                    </Table.Cell>
                                    <Table.Cell>
                                        {formatRupiah(transaction.total)}
                                    </Table.Cell>
                                    <Table.Cell>
                                        {formatDateToEnglish(
                                            transaction.created_at ?? "",
                                            true,
                                        )}
                                    </Table.Cell>
                                    <Table.Cell className="flex items-center justify-center gap-2">
                                        <Link
                                            href={`/transactions/${transaction.id}`}
                                        >
                                            <FaInfoCircle className="size-6 text-blue-500" />
                                        </Link>
                                    </Table.Cell>
                                </Table.Row>
                            ))}
                    </Table.Body>
                </Table>
                {isLoading && <DataLoading />}
                {transactions.data.length <= 0 && !isLoading && (
                    <DataNotFoundError />
                )}
            </div>
            {transactions.data.length > 0 && !isLoading && (
                <ListDataPagination data={transactions} params={params} />
            )}
        </BackpageLayout>
    );
}
