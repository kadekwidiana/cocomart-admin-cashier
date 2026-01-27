import DataNotFoundError from "@/Components/Error/DataNotFoundError";
import DataLoading from "@/Components/Loading/DataLoading";
import { InputNotificationModal } from "@/Components/Modal/InputNotificationModal";
import ListDataPagination from "@/Components/Pagination/ListDataPagination";
import { NOTIFICATION_TYPES, PER_PAGES } from "@/Constants/dataOptions";
import useDeleteNotification from "@/Features/Notifications/useDeletePromo";
import useGetNotifications from "@/Features/Notifications/useGetPromos";
import BackpageLayout from "@/Layouts/BackpageLayout";
import { Link } from "@inertiajs/react";
import { Button, Label, Select, Table, TextInput } from "flowbite-react";
import { FaInfoCircle, FaTrash } from "react-icons/fa";

export default function NotificationPage() {
    const { notifications, isLoading, params, handleChange, getData } =
        useGetNotifications();

    const { deleteDataConfirm } = useDeleteNotification();

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
                        <Label htmlFor="type" value="Type" />
                    </div>
                    <Select
                        id="type"
                        name="type"
                        value={params.type}
                        onChange={(e) => handleChange("type", e.target.value)}
                    >
                        <option value="">All</option>
                        {NOTIFICATION_TYPES.map((type) => (
                            <option key={type} value={type}>
                                {type}
                            </option>
                        ))}
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
                    <Link href="/notifications">
                        <Button
                            color="none"
                            type="button"
                            className="bg-red-700/80 hover:bg-red-700/100 text-white text-nowrap w-fit"
                        >
                            Reset
                        </Button>
                    </Link>

                    <InputNotificationModal
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
                        <Table.HeadCell>Title</Table.HeadCell>
                        <Table.HeadCell>Type</Table.HeadCell>
                        <Table.HeadCell>Image</Table.HeadCell>
                        <Table.HeadCell className="flex items-center justify-center">
                            Actions
                        </Table.HeadCell>
                    </Table.Head>
                    <Table.Body className="divide-y">
                        {!isLoading &&
                            notifications.data.map((notification, index) => (
                                <Table.Row key={index} className="bg-white">
                                    <Table.Cell className="w-5 whitespace-nowrap font-medium text-gray-900">
                                        {(notifications.current_page - 1) *
                                            notifications.per_page +
                                            index +
                                            1}
                                    </Table.Cell>
                                    <Table.Cell className="whitespace-nowrap font-medium text-gray-900">
                                        {notification.title ?? "-"}
                                    </Table.Cell>
                                    <Table.Cell>
                                        {notification.type ?? "-"}
                                    </Table.Cell>
                                    <Table.Cell>
                                        <img
                                            src={notification.image}
                                            alt="Image"
                                            className="w-28 rounded-lg object-cover"
                                        />
                                    </Table.Cell>
                                    <Table.Cell className="flex items-center justify-center gap-2">
                                        <InputNotificationModal
                                            trigger={
                                                <FaInfoCircle className="size-6 text-blue-500" />
                                            }
                                            isUpdate={true}
                                            data={notification}
                                        />
                                        <button
                                            onClick={() =>
                                                deleteDataConfirm(
                                                    notification.id,
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
                {notifications.data.length <= 0 && !isLoading && (
                    <DataNotFoundError />
                )}
            </div>
            {notifications.data.length > 0 && !isLoading && (
                <ListDataPagination data={notifications} params={params} />
            )}
        </BackpageLayout>
    );
}
