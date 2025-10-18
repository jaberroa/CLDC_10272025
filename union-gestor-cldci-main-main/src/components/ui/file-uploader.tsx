import { useState } from "react";
import { supabase } from "@/integrations/supabase/client";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Progress } from "@/components/ui/progress";
import { Badge } from "@/components/ui/badge";
import { 
  Upload, 
  File, 
  FileText, 
  Image, 
  FileSpreadsheet, 
  X, 
  Check,
  AlertCircle
} from "lucide-react";
import { cn } from "@/lib/utils";

interface UploadedFile {
  id: string;
  file: File;
  progress: number;
  status: 'uploading' | 'completed' | 'error';
  url?: string;
}

interface FileUploaderProps {
  onFilesUploaded?: (files: UploadedFile[]) => void;
  maxFiles?: number;
  maxSizePerFile?: number; // in MB
  acceptedTypes?: string[];
  bucketName?: string; // Supabase bucket name
  folderPath?: string; // Optional folder path within bucket
  className?: string;
}

const FileUploader = ({ 
  onFilesUploaded, 
  maxFiles = 10, 
  maxSizePerFile = 10,
  acceptedTypes = [
    '.pdf', '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx',
    '.txt', '.rtf', '.odt', '.ods', '.odp', '.csv',
    '.jpg', '.jpeg', '.png', '.gif', '.bmp', '.svg',
    '.zip', '.rar', '.7z'
  ],
  bucketName = 'expedientes',
  folderPath = '',
  className 
}: FileUploaderProps) => {
  const [files, setFiles] = useState<UploadedFile[]>([]);
  const [isDragging, setIsDragging] = useState(false);

  const getFileIcon = (fileName: string) => {
    const extension = fileName.toLowerCase().split('.').pop();
    
    switch (extension) {
      case 'pdf':
        return <FileText className="h-5 w-5 text-red-500" />;
      case 'doc':
      case 'docx':
        return <FileText className="h-5 w-5 text-blue-500" />;
      case 'xls':
      case 'xlsx':
      case 'csv':
        return <FileSpreadsheet className="h-5 w-5 text-green-500" />;
      case 'jpg':
      case 'jpeg':
      case 'png':
      case 'gif':
      case 'bmp':
      case 'svg':
        return <Image className="h-5 w-5 text-purple-500" />;
      default:
        return <File className="h-5 w-5 text-gray-500" />;
    }
  };

  const formatFileSize = (bytes: number) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  };

  const validateFile = (file: File): string | null => {
    // Check file size
    if (file.size > maxSizePerFile * 1024 * 1024) {
      return `El archivo excede el tamaño máximo de ${maxSizePerFile}MB`;
    }

    // Check file type
    const fileName = file.name.toLowerCase();
    const isValidType = acceptedTypes.some(type => 
      fileName.endsWith(type.replace('.', ''))
    );
    
    if (!isValidType) {
      return 'Tipo de archivo no permitido';
    }

    return null;
  };

  const uploadFile = async (file: File): Promise<UploadedFile> => {
    const fileId = Date.now().toString() + Math.random().toString(36).substr(2, 9);
    const fileName = `${fileId}_${file.name}`;
    const filePath = folderPath ? `${folderPath}/${fileName}` : fileName;
    
    const uploadedFile: UploadedFile = {
      id: fileId,
      file,
      progress: 0,
      status: 'uploading'
    };

    // Simulate progress for better UX
    const progressInterval = setInterval(() => {
      setFiles(prevFiles => 
        prevFiles.map(f => {
          if (f.id === fileId && f.progress < 90) {
            return { ...f, progress: f.progress + Math.random() * 30 };
          }
          return f;
        })
      );
    }, 200);

    try {
      // Upload to Supabase Storage
      const { data, error } = await supabase.storage
        .from(bucketName)
        .upload(filePath, file);

      clearInterval(progressInterval);

      if (error) {
        throw error;
      }

      // Get public URL if bucket is public, otherwise get signed URL
      const { data: { publicUrl } } = supabase.storage
        .from(bucketName)
        .getPublicUrl(filePath);

      setFiles(prevFiles => 
        prevFiles.map(f => 
          f.id === fileId ? { ...f, progress: 100, status: 'completed', url: publicUrl } : f
        )
      );
      
    } catch (error) {
      clearInterval(progressInterval);
      console.error('Upload error:', error);
      setFiles(prevFiles => 
        prevFiles.map(f => 
          f.id === fileId ? { ...f, status: 'error' } : f
        )
      );
    }

    return uploadedFile;
  };

  const handleFiles = async (fileList: FileList) => {
    const newFiles = Array.from(fileList);
    
    // Check max files limit
    if (files.length + newFiles.length > maxFiles) {
      alert(`Solo se pueden subir máximo ${maxFiles} archivos`);
      return;
    }

    const validFiles: File[] = [];
    const invalidFiles: { file: File; error: string }[] = [];

    newFiles.forEach(file => {
      const error = validateFile(file);
      if (error) {
        invalidFiles.push({ file, error });
      } else {
        validFiles.push(file);
      }
    });

    // Show validation errors
    if (invalidFiles.length > 0) {
      const errorMessages = invalidFiles.map(({ file, error }) => 
        `${file.name}: ${error}`
      ).join('\n');
      alert(`Algunos archivos no se pudieron cargar:\n${errorMessages}`);
    }

    // Upload valid files
    for (const file of validFiles) {
      const uploadedFile = await uploadFile(file);
      setFiles(prevFiles => [...prevFiles, uploadedFile]);
    }

    if (onFilesUploaded) {
      onFilesUploaded(files);
    }
  };

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(false);
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
      handleFiles(files);
    }
  };

  const handleDragOver = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(true);
  };

  const handleDragLeave = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(false);
  };

  const handleFileInput = (e: React.ChangeEvent<HTMLInputElement>) => {
    const files = e.target.files;
    if (files && files.length > 0) {
      handleFiles(files);
    }
  };

  const removeFile = (fileId: string) => {
    setFiles(prevFiles => prevFiles.filter(f => f.id !== fileId));
  };

  return (
    <div className={cn("space-y-4", className)}>
      {/* Upload Area */}
      <div
        className={cn(
          "border-2 border-dashed rounded-lg p-8 text-center transition-colors",
          isDragging 
            ? "border-primary bg-primary/5" 
            : "border-muted-foreground/25 hover:border-muted-foreground/50"
        )}
        onDrop={handleDrop}
        onDragOver={handleDragOver}
        onDragLeave={handleDragLeave}
      >
        <Upload className="h-12 w-12 mx-auto mb-4 text-muted-foreground" />
        <div className="space-y-2">
          <p className="text-lg font-medium">
            Arrastra archivos aquí o haz clic para seleccionar
          </p>
          <p className="text-sm text-muted-foreground">
            Máximo {maxFiles} archivos • Hasta {maxSizePerFile}MB por archivo
          </p>
          <div className="flex flex-wrap justify-center gap-1 mt-2">
            {acceptedTypes.slice(0, 8).map((type) => (
              <Badge key={type} variant="outline" className="text-xs">
                {type.toUpperCase()}
              </Badge>
            ))}
            {acceptedTypes.length > 8 && (
              <Badge variant="outline" className="text-xs">
                +{acceptedTypes.length - 8} más
              </Badge>
            )}
          </div>
        </div>
        
        <Input
          type="file"
          multiple
          accept={acceptedTypes.join(',')}
          onChange={handleFileInput}
          className="hidden"
          id="file-upload"
        />
        <Label htmlFor="file-upload" asChild>
          <Button variant="outline" className="mt-4">
            Seleccionar Archivos
          </Button>
        </Label>
      </div>

      {/* File List */}
      {files.length > 0 && (
        <div className="space-y-3">
          <Label className="text-sm font-medium">
            Archivos ({files.length}/{maxFiles})
          </Label>
          
          <div className="space-y-2">
            {files.map((uploadedFile) => (
              <div
                key={uploadedFile.id}
                className="flex items-center gap-3 p-3 border rounded-lg bg-muted/30"
              >
                {getFileIcon(uploadedFile.file.name)}
                
                <div className="flex-1 min-w-0">
                  <div className="flex items-center justify-between mb-1">
                    <p className="text-sm font-medium truncate">
                      {uploadedFile.file.name}
                    </p>
                    <div className="flex items-center gap-2">
                      {uploadedFile.status === 'completed' && (
                        <Check className="h-4 w-4 text-green-500" />
                      )}
                      {uploadedFile.status === 'error' && (
                        <AlertCircle className="h-4 w-4 text-red-500" />
                      )}
                      <Button
                        variant="ghost"
                        size="sm"
                        onClick={() => removeFile(uploadedFile.id)}
                        className="h-6 w-6 p-0"
                      >
                        <X className="h-3 w-3" />
                      </Button>
                    </div>
                  </div>
                  
                  <div className="flex items-center gap-2 text-xs text-muted-foreground">
                    <span>{formatFileSize(uploadedFile.file.size)}</span>
                    {uploadedFile.status === 'uploading' && (
                      <>
                        <span>•</span>
                        <span>{Math.round(uploadedFile.progress)}%</span>
                      </>
                    )}
                    {uploadedFile.status === 'completed' && (
                      <>
                        <span>•</span>
                        <span className="text-green-600">Completado</span>
                      </>
                    )}
                    {uploadedFile.status === 'error' && (
                      <>
                        <span>•</span>
                        <span className="text-red-600">Error</span>
                      </>
                    )}
                  </div>
                  
                  {uploadedFile.status === 'uploading' && (
                    <Progress 
                      value={uploadedFile.progress} 
                      className="h-1 mt-2"
                    />
                  )}
                </div>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
};

export { FileUploader };