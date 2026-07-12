import useUpdateStatus from "@/Features/Transactions/useUpdateStatus";
import { Button, Label, Modal, Select } from "flowbite-react";
import { useState } from "react";

const TITLES = {
    transaction: "Update Transaction Status",
    pickup: "Update Pickup Status",
    shipment: "Update Shipment Status",
};

export function UpdateStatusModal({ trigger, type, transaction }) {
    const [openModal, setOpenModal] = useState(false);

    const {
        status,
        options,
        isSubmitting,
        errors,
        handleChange,
        handleSubmit,
    } = useUpdateStatus(setOpenModal, type, transaction);

    return (
        <>
            <div className="cursor-pointer" onClick={() => setOpenModal(true)}>
                {trigger}
            </div>
            <Modal show={openModal} onClose={() => setOpenModal(false)}>
                <Modal.Header>{TITLES[type]}</Modal.Header>
                <Modal.Body>
                    <form
                        onSubmit={handleSubmit}
                        className="flex w-full flex-col gap-3"
                    >
                        <div>
                            <div className="mb-2 block">
                                <Label
                                    htmlFor="status"
                                    value="Status*"
                                    color={errors.status ? "failure" : "gray"}
                                />
                            </div>
                            <Select
                                id="status"
                                name="status"
                                value={status}
                                onChange={handleChange}
                                color={errors.status ? "failure" : "gray"}
                                helperText={errors.status}
                            >
                                {options.map((option) => (
                                    <option
                                        key={option.value}
                                        value={option.value}
                                    >
                                        {option.value}
                                    </option>
                                ))}
                            </Select>
                        </div>

                        <div className="flex items-center justify-end gap-3">
                            <Button
                                onClick={() => setOpenModal(false)}
                                color="none"
                                className="border-primary/100 border hover:bg-primary/10 text-primary/100 text-nowrap"
                            >
                                Cancel
                            </Button>
                            <Button
                                type="submit"
                                disabled={isSubmitting}
                                color="none"
                                className="bg-primary hover:bg-yellow-500 text-white text-nowrap"
                            >
                                Update
                            </Button>
                        </div>
                    </form>
                </Modal.Body>
            </Modal>
        </>
    );
}
