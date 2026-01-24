import useInputImageSlider from "@/Features/ImageSliders/useInputImageSlider";
import { formatDateToEnglish } from "@/Utils/formatDateToEnglish";
import { Button, Label, Modal, Select, TextInput } from "flowbite-react";
import { useState } from "react";

export function InputImageSliderModal({
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
    } = useInputImageSlider(setOpenModal, isUpdate, data);

    return (
        <>
            <div className="cursor-pointer" onClick={() => setOpenModal(true)}>
                {trigger}
            </div>
            <Modal show={openModal} onClose={() => setOpenModal(false)}>
                <Modal.Header>
                    {isUpdate ? "Detail Image Slider" : "Add Image Slider"}
                </Modal.Header>
                <Modal.Body>
                    <form
                        onSubmit={handleSubmit}
                        className="flex w-full flex-col gap-3"
                    >
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
                                    htmlFor="link"
                                    value="Link*"
                                    color={errors.link ? "failure" : "gray"}
                                />
                            </div>
                            <TextInput
                                id="link"
                                name="link"
                                type="text"
                                placeholder="Enter link..."
                                value={formData.link}
                                onChange={handleChange}
                                color={errors.link ? "failure" : "gray"}
                                helperText={errors.link}
                            />
                        </div>

                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-2">
                            <div>
                                <div className="mb-2 block">
                                    <Label
                                        htmlFor="index"
                                        value="Index*"
                                        color={
                                            errors.index ? "failure" : "gray"
                                        }
                                    />
                                </div>
                                <TextInput
                                    id="index"
                                    name="index"
                                    type="text"
                                    placeholder="Enter index..."
                                    value={formData.index}
                                    onChange={handleChange}
                                    color={errors.index ? "failure" : "gray"}
                                    helperText={errors.index}
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
