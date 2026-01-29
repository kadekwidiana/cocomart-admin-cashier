import DataNotFoundError from "@/Components/Error/DataNotFoundError";
import DataLoading from "@/Components/Loading/DataLoading";
import { InputUserModal } from "@/Components/Modal/InputUserModal";
import ListDataPagination from "@/Components/Pagination/ListDataPagination";
import { PER_PAGES } from "@/Constants/dataOptions";
import useDeleteUser from "@/Features/Users/useDeleteUser";
import useGetUsers from "@/Features/Users/useGetUsers";
import BackpageLayout from "@/Layouts/BackpageLayout";
import { Link, usePage } from "@inertiajs/react";
import { Button, Label, Select, Table, TextInput } from "flowbite-react";
import { FaInfoCircle, FaTrash } from "react-icons/fa";

export default function UserPage() {
    const { locations } = usePage().props;

    const { users, isLoading, params, handleChange, getData } = useGetUsers();

    const { deleteDataConfirm } = useDeleteUser();

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
                        <Label htmlFor="role" value="Role" />
                    </div>
                    <Select
                        id="role"
                        name="role"
                        value={params.role}
                        onChange={(e) => handleChange("role", e.target.value)}
                    >
                        <option value="">All</option>
                        <option value="CASHIER">CASHIER</option>
                        <option value="ADMIN">ADMIN</option>
                    </Select>
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
                <div>
                    <div className="mb-2 block">
                        <Label htmlFor="oxy_location_id" value="Location" />
                    </div>
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
                                <option key={location.id} value={location.id}>
                                    {location.name}
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
                    <Link href="/users">
                        <Button
                            color="none"
                            type="button"
                            className="bg-red-700/80 hover:bg-red-700/100 text-white text-nowrap w-fit"
                        >
                            Reset
                        </Button>
                    </Link>

                    <InputUserModal
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
                        <Table.HeadCell>Name</Table.HeadCell>
                        <Table.HeadCell>Email</Table.HeadCell>
                        <Table.HeadCell>Role</Table.HeadCell>
                        <Table.HeadCell>Status</Table.HeadCell>
                        <Table.HeadCell>Location</Table.HeadCell>
                        <Table.HeadCell className="flex items-center justify-center">
                            Actions
                        </Table.HeadCell>
                    </Table.Head>
                    <Table.Body className="divide-y">
                        {!isLoading &&
                            users.data.map((user, index) => (
                                <Table.Row key={index} className="bg-white">
                                    <Table.Cell className="w-5 whitespace-nowrap font-medium text-gray-900">
                                        {(users.current_page - 1) *
                                            users.per_page +
                                            index +
                                            1}
                                    </Table.Cell>
                                    <Table.Cell className="whitespace-nowrap font-medium text-gray-900">
                                        {user.name}
                                    </Table.Cell>
                                    <Table.Cell>{user.email}</Table.Cell>
                                    <Table.Cell>{user.role}</Table.Cell>
                                    <Table.Cell>
                                        {user.is_active ? "Active" : "Inactive"}
                                    </Table.Cell>
                                    <Table.Cell>
                                        {locations.find(
                                            (location) =>
                                                location.id ===
                                                user.oxy_location_id,
                                        )?.name ?? "-"}
                                    </Table.Cell>
                                    <Table.Cell className="flex items-center justify-center gap-2">
                                        <InputUserModal
                                            trigger={
                                                <FaInfoCircle className="size-6 text-blue-500" />
                                            }
                                            isUpdate={true}
                                            data={user}
                                        />
                                        <button
                                            onClick={() =>
                                                deleteDataConfirm(user.id)
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
                {users.data.length <= 0 && !isLoading && <DataNotFoundError />}
            </div>
            {users.data.length > 0 && !isLoading && (
                <ListDataPagination data={users} params={params} />
            )}
        </BackpageLayout>
    );
}
