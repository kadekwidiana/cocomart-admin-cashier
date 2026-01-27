import { NOTIFICATION_TYPES } from "@/Constants/dataOptions";
import useInputNotification from "@/Features/Notifications/useInputPromo";
import { formatDateToEnglish } from "@/Utils/formatDateToEnglish";
import {
    Button,
    Label,
    Modal,
    Select,
    Textarea,
    TextInput,
} from "flowbite-react";
import { useState } from "react";

export function InputNotificationModal({
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
    } = useInputNotification(setOpenModal, isUpdate, data);

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
                    {isUpdate ? "Detail Notification" : "Add Notification"}
                </Modal.Header>
                <Modal.Body>
                    <form
                        onSubmit={handleSubmit}
                        className="flex w-full flex-col gap-3"
                    >
                        <div>
                            <div className="mb-2 block">
                                <Label
                                    htmlFor="title"
                                    value="Title*"
                                    color={errors.title ? "failure" : "gray"}
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
                                    htmlFor="type"
                                    value="Type*"
                                    color={errors.type ? "failure" : "gray"}
                                />
                            </div>
                            <Select
                                id="type"
                                name="type"
                                onChange={handleChange}
                                color={errors.type ? "failure" : "gray"}
                                helperText={errors.type}
                                defaultValue={formData.type}
                            >
                                {NOTIFICATION_TYPES.map((type) => (
                                    <option key={type} value={type}>
                                        {type}
                                    </option>
                                ))}
                            </Select>
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

                        <div>
                            <div className="mb-2 block">
                                <Label
                                    htmlFor="body"
                                    value="Body*"
                                    color={errors.body ? "failure" : "gray"}
                                />
                            </div>
                            <Textarea
                                id="body"
                                name="body"
                                placeholder="Enter body..."
                                value={formData.body}
                                onChange={handleChange}
                                color={errors.body ? "failure" : "gray"}
                                helperText={errors.body}
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
                                    className="bg-primary/80 hover:bg-primary text-white text-nowrap"
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
