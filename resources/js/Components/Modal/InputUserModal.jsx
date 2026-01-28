import { NOTIFICATION_TYPES } from "@/Constants/dataOptions";
import useInputNotification from "@/Features/Notifications/useInputPromo";
import useInputUser from "@/Features/Users/useInputUser";
import { formatDateToEnglish } from "@/Utils/formatDateToEnglish";
import { usePage } from "@inertiajs/react";
import {
    Button,
    Label,
    Modal,
    Select,
    Textarea,
    TextInput,
} from "flowbite-react";
import { useState } from "react";

export function InputUserModal({
    trigger,
    isUpdate = false,
    data,
    isReadOnly = false,
}) {
    const { locations } = usePage().props;
    const [openModal, setOpenModal] = useState(false);

    const { formData, isSubmitting, errors, handleChange, handleSubmit } =
        useInputUser(setOpenModal, isUpdate, data);

    return (
        <>
            <div className="cursor-pointer" onClick={() => setOpenModal(true)}>
                {trigger}
            </div>
            <Modal show={openModal} onClose={() => setOpenModal(false)}>
                <Modal.Header>
                    {isUpdate ? "Detail User" : "Add User"}
                </Modal.Header>
                <Modal.Body>
                    <form
                        onSubmit={handleSubmit}
                        className="flex w-full flex-col gap-3"
                    >
                        <div>
                            <div className="mb-2 block">
                                <Label
                                    htmlFor="name"
                                    value="Name*"
                                    color={errors.name ? "failure" : "gray"}
                                />
                            </div>
                            <TextInput
                                id="name"
                                name="name"
                                type="text"
                                placeholder="Enter name..."
                                value={formData.name}
                                onChange={handleChange}
                                color={errors.name ? "failure" : "gray"}
                                helperText={errors.name}
                            />
                        </div>
                        <div>
                            <div className="mb-2 block">
                                <Label
                                    htmlFor="email"
                                    value="Email*"
                                    color={errors.email ? "failure" : "gray"}
                                />
                            </div>
                            <TextInput
                                id="email"
                                name="email"
                                type="email"
                                placeholder="Enter email..."
                                value={formData.email}
                                onChange={handleChange}
                                color={errors.email ? "failure" : "gray"}
                                helperText={errors.email}
                            />
                        </div>
                        <div>
                            <div className="mb-2 block">
                                <Label
                                    htmlFor="password"
                                    value="Password*"
                                    color={errors.password ? "failure" : "gray"}
                                />
                            </div>
                            <TextInput
                                id="password"
                                name="password"
                                type="text"
                                placeholder="Enter password..."
                                value={formData.password}
                                onChange={handleChange}
                                color={errors.password ? "failure" : "gray"}
                                helperText={errors.password}
                            />
                        </div>
                        <div>
                            <div className="mb-2 block">
                                <Label
                                    htmlFor="role"
                                    value="Role*"
                                    color={errors.role ? "failure" : "gray"}
                                />
                            </div>
                            <Select
                                id="role"
                                name="role"
                                onChange={handleChange}
                                color={errors.role ? "failure" : "gray"}
                                helperText={errors.role}
                                defaultValue={formData.role}
                            >
                                <option value="CASHIER">CASHIER</option>
                                <option value="ADMIN">ADMIN</option>
                            </Select>
                        </div>

                        {formData.role === "CASHIER" && (
                            <div>
                                <div className="mb-2 block">
                                    <Label
                                        htmlFor="oxy_location_id"
                                        value="Location*"
                                        color={
                                            errors.oxy_location_id
                                                ? "failure"
                                                : "gray"
                                        }
                                    />
                                </div>
                                <Select
                                    id="oxy_location_id"
                                    name="oxy_location_id"
                                    onChange={handleChange}
                                    color={
                                        errors.oxy_location_id
                                            ? "failure"
                                            : "gray"
                                    }
                                    helperText={errors.oxy_location_id}
                                    defaultValue={formData.oxy_location_id}
                                >
                                    <option value="">Select Location</option>
                                    {locations.map((location) => (
                                        <option
                                            key={location.id}
                                            value={location.id}
                                        >
                                            {location.name}
                                        </option>
                                    ))}
                                </Select>
                            </div>
                        )}

                        <div>
                            <div className="mb-2 block">
                                <Label
                                    htmlFor="is_active"
                                    value="Status*"
                                    color={
                                        errors.is_active ? "failure" : "gray"
                                    }
                                />
                            </div>
                            <Select
                                id="is_active"
                                name="is_active"
                                onChange={handleChange}
                                color={errors.is_active ? "failure" : "gray"}
                                helperText={errors.is_active}
                                defaultValue={formData.is_active}
                            >
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </Select>
                        </div>

                        <div>
                            <div className="mb-2 block">
                                <Label
                                    htmlFor="phone_number"
                                    value="Phone Number"
                                    color={
                                        errors.phone_number ? "failure" : "gray"
                                    }
                                />
                            </div>
                            <TextInput
                                id="phone_number"
                                name="phone_number"
                                type="number"
                                placeholder="Enter phone number..."
                                value={formData.phone_number}
                                onChange={handleChange}
                                color={errors.phone_number ? "failure" : "gray"}
                                helperText={errors.phone_number}
                            />
                        </div>
                        <div>
                            <div className="mb-2 block">
                                <Label
                                    htmlFor="address"
                                    value="Address"
                                    color={errors.address ? "failure" : "gray"}
                                />
                            </div>
                            <Textarea
                                id="address"
                                name="address"
                                placeholder="Enter address..."
                                value={formData.address}
                                onChange={handleChange}
                                color={errors.address ? "failure" : "gray"}
                                helperText={errors.address}
                            />
                        </div>

                        {isUpdate && (
                            <div className="grid grid-cols-1 lg:grid-cols-2 gap-3 w-full">
                                <div>
                                    <div className="mb-2 block">
                                        <Label
                                            htmlFor="created_at"
                                            value="Created At"
                                        />
                                    </div>
                                    <TextInput
                                        id="created_at"
                                        name="created_at"
                                        type="text"
                                        value={formatDateToEnglish(
                                            data.created_at ?? "",
                                        )}
                                        readOnly
                                    />
                                </div>
                                <div>
                                    <div className="mb-2 block">
                                        <Label
                                            htmlFor="updated_at"
                                            value="Updated At"
                                        />
                                    </div>
                                    <TextInput
                                        id="updated_at"
                                        name="updated_at"
                                        type="text"
                                        value={formatDateToEnglish(
                                            data.updated_at ?? "",
                                        )}
                                        readOnly
                                    />
                                </div>
                            </div>
                        )}
                        <div className="flex items-center justify-end gap-3">
                            <Button
                                onClick={() => setOpenModal(false)}
                                color="none"
                                className="border-primary/100 border hover:bg-primary/10 text-primary/100 text-nowrap"
                            >
                                Cancel
                            </Button>
                            {!isReadOnly && (
                                <Button
                                    type="submit"
                                    disabled={isSubmitting}
                                    color="none"
                                    className="bg-primary hover:bg-yellow-500 text-white text-nowrap"
                                >
                                    {isUpdate ? "Update" : "Save"}
                                </Button>
                            )}
                        </div>
                    </form>
                </Modal.Body>
            </Modal>
        </>
    );
}
