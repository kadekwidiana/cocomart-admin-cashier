import useInputPromo from "@/Features/Promos/useInputPromo";
import { formatDateToEnglish } from "@/Utils/formatDateToEnglish";
import { Button, Label, Modal, Select, TextInput } from "flowbite-react";
import { useState } from "react";

export function InputPromoModal({
    trigger,
    isUpdate = false,
    data,
    isReadOnly = false,
}) {
    const [openModal, setOpenModal] = useState(false);

    const {
        formData,
        imagePreview,
        isSubmitting,
        errors,
        handleChange,
        handleFileChange,
        handleSubmit,
    } = useInputPromo(setOpenModal, isUpdate, data);

    return (
        <>
            <div className="cursor-pointer" onClick={() => setOpenModal(true)}>
                {trigger}
            </div>
            <Modal
                show={openModal}
                onClose={() => setOpenModal(false)}
                size="5xl"
            >
                <Modal.Header>
                    {isUpdate ? "Detail Promo" : "Add Promo"}
                </Modal.Header>
                <Modal.Body>
                    <form
                        onSubmit={handleSubmit}
                        className="flex w-full flex-col gap-3"
                    >
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-2">
                            <div>
                                <div className="mb-2 block">
                                    <Label
                                        htmlFor="title"
                                        value="Title*"
                                        color={
                                            errors.title ? "failure" : "gray"
                                        }
                                    />
                                </div>
                                <TextInput
                                    id="title"
                                    name="title"
                                    type="text"
                                    placeholder="Enter title..."
                                    value={formData.title}
                                    onChange={handleChange}
                                    color={errors.title ? "failure" : "gray"}
                                    helperText={errors.title}
                                />
                            </div>
                            <div>
                                <div className="mb-2 block">
                                    <Label
                                        htmlFor="code"
                                        value="Code*"
                                        color={errors.code ? "failure" : "gray"}
                                    />
                                </div>
                                <TextInput
                                    id="code"
                                    name="code"
                                    type="text"
                                    placeholder="Enter code..."
                                    value={formData.code}
                                    onChange={handleChange}
                                    color={errors.code ? "failure" : "gray"}
                                    helperText={errors.code}
                                />
                            </div>
                        </div>
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-2">
                            <div>
                                <div className="mb-2 block">
                                    <Label
                                        htmlFor="start_date"
                                        value="Start Date*"
                                        color={
                                            errors.start_date
                                                ? "failure"
                                                : "gray"
                                        }
                                    />
                                </div>
                                <TextInput
                                    id="start_date"
                                    name="start_date"
                                    type="date"
                                    placeholder="Enter start_date..."
                                    value={formData.start_date}
                                    onChange={handleChange}
                                    color={
                                        errors.start_date ? "failure" : "gray"
                                    }
                                    helperText={errors.start_date}
                                />
                            </div>
                            <div>
                                <div className="mb-2 block">
                                    <Label
                                        htmlFor="end_date"
                                        value="End Date*"
                                        color={
                                            errors.end_date ? "failure" : "gray"
                                        }
                                    />
                                </div>
                                <TextInput
                                    id="end_date"
                                    name="end_date"
                                    type="date"
                                    placeholder="Enter end_date..."
                                    value={formData.end_date}
                                    onChange={handleChange}
                                    color={errors.end_date ? "failure" : "gray"}
                                    helperText={errors.end_date}
                                />
                            </div>
                        </div>
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-2">
                            <div>
                                <div className="mb-2 block">
                                    <Label
                                        htmlFor="discount_percentage"
                                        value="Discount Percentage (%)*"
                                        color={
                                            errors.discount_percentage
                                                ? "failure"
                                                : "gray"
                                        }
                                    />
                                </div>
                                <TextInput
                                    id="discount_percentage"
                                    name="discount_percentage"
                                    type="number"
                                    placeholder="Enter discount percentage..."
                                    value={formData.discount_percentage}
                                    onChange={handleChange}
                                    color={
                                        errors.discount_percentage
                                            ? "failure"
                                            : "gray"
                                    }
                                    helperText={errors.discount_percentage}
                                />
                            </div>
                            <div>
                                <div className="mb-2 block">
                                    <Label
                                        htmlFor="is_active"
                                        value="Status*"
                                        color={
                                            errors.is_active
                                                ? "failure"
                                                : "gray"
                                        }
                                    />
                                </div>
                                <Select
                                    id="is_active"
                                    name="is_active"
                                    onChange={handleChange}
                                    color={
                                        errors.is_active ? "failure" : "gray"
                                    }
                                    helperText={errors.is_active}
                                    defaultValue={formData.is_active}
                                >
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </Select>
                            </div>
                        </div>

                        <div>
                            {imagePreview.image && (
                                <div className="mt-2">
                                    <img
                                        src={imagePreview.image}
                                        alt="Preview"
                                        className="w-1/2 rounded-lg object-cover"
                                    />
                                </div>
                            )}
                        </div>
                        <div>
                            <div className="mb-2 block">
                                <Label
                                    htmlFor="image"
                                    value="Image*"
                                    color={errors.image ? "failure" : "gray"}
                                />
                            </div>
                            <TextInput
                                id="image"
                                name="image"
                                type="file"
                                onChange={handleFileChange}
                                color={errors.image ? "failure" : "gray"}
                                helperText={errors.image}
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
